<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CalculatorController extends Controller
{
    public function showFormCalculator()
    {
        return view('auth.calculator');
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'usia' => 'required|integer',
            'jenis_kelamin' => 'required|string',
            'tb' => 'required|numeric',
            'bb' => 'required|numeric',
            'aktivitas' => 'required|string',
        ]);
        $userId = Auth::id();

        // cek apakah sudah ada data untuk user ini
        $calc = User::where('id', $userId)->first();

        if ($calc) {
            // jika sudah ada, update data
            $calc->update($validated);
        } else {
            // jika belum ada, tambahkan data baru
            $validated['id'] = $userId;
            User::create($validated);
        }
    }

    public function monitor()
    {
        $user = Auth::user();

        if (!$user || is_null($user->bb) || is_null($user->tb) || is_null($user->usia) || is_null($user->jenis_kelamin) || is_null($user->aktivitas)) {
            return null;
        }

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

        $today = date('Y-m-d');

        $todayCalories = DB::table('daily_calories')
            ->where('date', $today)
            ->sum('calories');

        return view('auth.calc_monitor', compact('bmi', 'bmr', 'tdee', 'todayCalories'));
    }
}