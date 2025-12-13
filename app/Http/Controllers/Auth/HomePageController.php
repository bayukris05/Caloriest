<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HomePageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showHomepage(Request $request)
    {
        $user = Auth::user();

        $hour = Carbon::now()->hour;
        // Set locale ke Indonesia
        Carbon::setLocale('id');
        $currentDateTime = Carbon::now()->isoFormat('D MMM Y, HH:mm');
        $currentDay = Carbon::now()->isoFormat('dddd');
        $user = Auth::user();

        // Cek kelengkapan profil
        $isProfileComplete = true;
        if (!$user || is_null($user->bb) || is_null($user->tb) || is_null($user->usia) || is_null($user->jenis_kelamin) || is_null($user->aktivitas)) {
            $isProfileComplete = false;
        }

        // Default values
        $bmi = 0;
        $bmr = 0;
        $tdee = 0;
        $calorieNeeds = 0;
        $calorieConsumed = 0;
        $caloriePercentage = 0;
        $lessCalorie = 0;
        $chartData = array_fill(0, 7, 0);
        $targetData = array_fill(0, 7, 0);

        if ($hour >= 5 && $hour < 11) {
            $greeting = 'SELAMAT PAGI';
        } elseif ($hour >= 11 && $hour < 15) {
            $greeting = 'SELAMAT SIANG';
        } elseif ($hour >= 15 && $hour < 18) {
            $greeting = 'SELAMAT SORE';
        } else {
            $greeting = 'SELAMAT MALAM';
        }

        if ($isProfileComplete) {
            $tinggi_meter = $user->tb / 100; // konversi cm ke meter
            $bmi = $user->bb / ($tinggi_meter * $tinggi_meter);

            // Hitung BMR (sesuai rumus di gambar)
            if ($user->jenis_kelamin == 'L') { // Laki-laki (atau 'male')
                // BMR = 88.362 + (13.397 × bb(kg)) + (4.799 × tb(cm)) - (5.677 × usia(tahun))
                $bmr = 88.362 + (13.397 * $user->bb) + (4.799 * $user->tb) - (5.677 * $user->usia);
            } else { // Perempuan (atau 'female')
                // BMR = 447.593 + (9.247 × bb(kg)) + (3.098 × tb(cm)) - (4.330 × usia)
                $bmr = 447.593 + (9.247 * $user->bb) + (3.098 * $user->tb) - (4.330 * $user->usia);
            }

            // Faktor aktivitas - gunakan nilai langsung seperti di form HTML
            $activityFactors = [
                '1.2' => 1.2,        // Sedentary (little or no exercise)
                '1.375' => 1.375,    // Lightly active (light exercise 1-3 days/week)
                '1.55' => 1.55,      // Moderately active (moderate exercise 3-5 days/week)
                '1.725' => 1.725,    // Very active (hard exercise 6-7 days/week)
                '1.9' => 1.9         // Extra active (very hard exercise & physical job)
            ];

            // Ambil faktor aktivitas (default: 1.2 jika tidak valid)
            $factor = $activityFactors[$user->aktivitas] ?? 1.2;

            // Hitung TDEE = BMR × Tingkat Aktivitas
            $tdee = $bmr * $factor;

            // Bulatkan hasil untuk display yang lebih rapi
            $bmi = round($bmi, 1);      // BMI dengan 1 angka desimal
            $bmr = round($bmr);         // BMR dibulatkan ke bilangan bulat
            $tdee = round($tdee);       // TDEE dibulatkan ke bilangan bulat


            $calorieNeeds = $tdee;
            $today = date('Y-m-d');

            $calorieConsumed = DB::table('daily_calories')
                ->where('date', $today)
                ->sum('calories');


            $caloriePercentage = $calorieNeeds ? round(($calorieConsumed / $calorieNeeds) * 100) : 0;

            $lessCalorie = $calorieNeeds - $calorieConsumed;

            $startDate = Carbon::now()->startOfWeek(); // atau ->subDays(6) kalau mau 6 hari ke belakang dari hari ini
            $endDate = Carbon::now()->endOfWeek();

            // Ambil total kalori per hari
            $caloriesPerDay = DB::table('daily_calories')
                ->select(DB::raw('DATE(date) as date'), DB::raw('SUM(calories) as total'))
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Siapkan data array untuk 7 hari
            $weekDays = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
            $chartData = [];

            foreach ($weekDays as $index => $day) {
                $date = $startDate->copy()->addDays($index)->format('Y-m-d');
                $dayData = $caloriesPerDay->firstWhere('date', $date);
                $chartData[] = $dayData ? (int)$dayData->total : 0;
            }

            $targetPerDay = $tdee;

            // Buat array target untuk 7 hari (untuk chart)
            $targetData = array_fill(0, 7, $targetPerDay);

            // FOOD RECOMMENDATION LOGIC - SMART DISTRIBUTION
            // Target: Memenuhi kebutuhan kalori harian (TDEE) dengan distribusi sehat
            // Pagi (Sarapan): ~30%
            // Siang (Makan Siang): ~40%
            // Malam (Makan Malam): ~30%
            
            $mealPlan = [];
            $allMenus = \App\Models\Menu::all(); // Ambil semua menu untuk dipilih
            
            if ($allMenus->count() > 0) {
                // Target kalori per waktu makan
                $targets = [
                    ['time' => '07:00 am', 'percent' => 0.30, 'label' => 'Sarapan'],
                    ['time' => '01:00 pm', 'percent' => 0.40, 'label' => 'Makan Siang'],
                    ['time' => '07:00 pm', 'percent' => 0.30, 'label' => 'Makan Malam'],
                ];

                foreach ($targets as $target) {
                    $targetCal = $tdee * $target['percent'];
                    $bestMenu = null;
                    $minDiff = PHP_INT_MAX;

                    // Cari menu dengan kalori paling mendekati target
                    // Logika ini mencari menu tunggal yang mendekati target. 
                    // Jika menu kecil-kecil, mungkin perlu kombinasi, tapi untuk simplifikasi kita cari 1 menu utama.
                    foreach ($allMenus as $menu) {
                        $cal = intval(preg_replace('/[^0-9]/', '', $menu->calories));
                        if ($cal > 0) {
                            $diff = abs($targetCal - $cal);
                            if ($diff < $minDiff) {
                                $minDiff = $diff;
                                $bestMenu = $menu;
                            }
                        }
                    }

                    if ($bestMenu) {
                        // Hapus menu terpilih dari list agar tidak dipilih lagi (opsional, jika stok menu cukup)
                        $allMenus = $allMenus->reject(function ($value, $key) use ($bestMenu) {
                            return $value->id == $bestMenu->id;
                        });

                        $mealPlan[] = [
                            'time' => $target['time'],
                            'activity' => $target['label'] . ': ' . $bestMenu->name . ' (' . $bestMenu->calories . ')',
                            'calories' => $bestMenu->calories
                        ];
                    }
                }
                
                // Urutkan berdasarkan waktu (walaupun array targets sudah urut)
                usort($mealPlan, function($a, $b) {
                    return strtotime($a['time']) - strtotime($b['time']);
                });
            }
        } else {
             $mealPlan = []; // Kosongkan jika profil belum lengkap
        }

        return view('auth.homepage', compact(
            'greeting',
            'currentDateTime',
            'currentDay',
            'user',
            'calorieNeeds',
            'caloriePercentage',
            'lessCalorie',
            'calorieConsumed',
            'bmi',
            'bmr',
            'tdee',
            'chartData',
            'targetData',
            'isProfileComplete',
            'mealPlan'
        ));
    }


    private function calculateCalorieNeeds($user) {}
}