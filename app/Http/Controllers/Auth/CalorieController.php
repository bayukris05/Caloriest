<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Routing\Controller;

class CalorieController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Calculate TDEE (you should have this logic somewhere)
        $tdee = $this->calculateTDEE($user);

        // Get today's total calories
        $todayCalories = DB::table('daily_calories')
            ->where('user_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->sum('calories');



        return view('calorie-tracker', compact('tdee', 'todayCalories'));
    }

    public function searchMenu(Request $request)
    {
        $query = $request->input('query');

        $menus = DB::table('menu')
            ->where('nama_menu', 'LIKE', '%' . $query . '%')
            ->select('id_menu as id', 'nama_menu as nama', 'jumlah_kalori as kalori')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'menus' => $menus
        ]);
    }

    public function addCalories(Request $request)
    {
        try {
            $request->validate([
                'menu_name' => 'required|string|max:255'
            ]);

            $user = Auth::user();
            $menuName = trim($request->menu_name);

            Log::info('=== DEBUG START ===');
            Log::info('Input menu name: "' . $menuName . '"');
            Log::info('Menu name length: ' . strlen($menuName));
            Log::info('Menu name encoded: ' . json_encode($menuName));

            // Coba beberapa query yang berbeda
            Log::info('=== TRYING DIFFERENT QUERIES ===');

            // Query 1: Exact match
            $menu1 = DB::table('menu')
                ->where('nama_menu', $menuName)
                ->first();
            Log::info('Query 1 (exact): ' . ($menu1 ? 'FOUND' : 'NOT FOUND'));
            // Query 2: Case insensitive
            $menu2 = DB::table('menu')
                ->whereRaw('LOWER(nama_menu) = LOWER(?)', [$menuName])
                ->first();
            Log::info('Query 2 (case insensitive): ' . ($menu2 ? 'FOUND' : 'NOT FOUND'));

            // Query 3: LIKE
            $menu3 = DB::table('menu')
                ->where('nama_menu', 'LIKE', '%' . $menuName . '%')
                ->first();
            Log::info('Query 3 (LIKE): ' . ($menu3 ? 'FOUND' : 'NOT FOUND'));

            // Query 4: Trim both sides
            $menu4 = DB::table('menu')
                ->whereRaw('TRIM(LOWER(nama_menu)) = TRIM(LOWER(?))', [$menuName])
                ->first();
            Log::info('Query 4 (trim + case insensitive): ' . ($menu4 ? 'FOUND' : 'NOT FOUND'));

            // Show all menus for comparison
            $allMenus = DB::table('menu')->select('nama_menu')->get()->pluck('nama_menu');
            Log::info('All available menus: ' . json_encode($allMenus));

            // Use the first successful query
            $menu = $menu1 ?? $menu2 ?? $menu3 ?? $menu4;

            if (!$menu) {
                Log::info('=== MENU NOT FOUND ===');
                return response()->json([
                    'success' => false,
                    'message' => 'Menu not found in database',
                    'searched_for' => $menuName,
                    'debug' => [
                        'input_length' => strlen($menuName),
                        'available_menus' => $allMenus->toArray(),
                        'queries_tried' => [
                            'exact' => $menu1 ? 'found' : 'not found',
                            'case_insensitive' => $menu2 ? 'found' : 'not found',
                            'like' => $menu3 ? 'found' : 'not found',
                            'trim_case' => $menu4 ? 'found' : 'not found'
                        ]
                    ]
                ]);
            }

            Log::info('=== MENU FOUND ===');
            Log::info('Found menu: ' . json_encode($menu));

            // Add calories to daily_calories table
            DB::table('daily_calories')->insert([
                'user_id' => $user->id,
                'menu_id' => $menu->id_menu,
                'calories' => $menu->jumlah_kalori,
                'date' => Carbon::today(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            // Get total calories for today
            $totalToday = DB::table('daily_calories')
                ->where('user_id', $user->id)
                ->whereDate('date', Carbon::today())
                ->sum('calories');

            Log::info('Calories added successfully. Total today: ' . $totalToday);
            Log::info('=== DEBUG END ===');

            return response()->json([
                'success' => true,
                'menu' => [
                    'nama' => $menu->nama_menu,
                    'kalori' => $menu->jumlah_kalori
                ],
                'total_today' => $totalToday,
                'message' => 'Calories added successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in addCalories: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
                'debug' => 'Check Laravel logs for details'
            ], 500);
        }
    }

    public function getCalorieHistory()
    {
        $user = Auth::user();

        $history = DB::table('daily_calories')
            ->join('menu', 'daily_calories.menu_id', '=', 'menu.id_menu')
            ->where('daily_calories.user_id', $user->id)
            ->whereDate('daily_calories.date', Carbon::today())
            ->select(
                'menu.nama_menu as menu_name',
                'daily_calories.calories',
                'daily_calories.created_at'
            )
            ->orderBy('daily_calories.created_at', 'desc')
            ->get();

        $totalToday = $history->sum('calories');

        return view('calorie-history', compact('history', 'totalToday'));
    }

    private function calculateTDEE($user)
    {
        // Your TDEE calculation logic here
        // This is just an example, adjust according to your existing logic

        if (!isset($user->weight) || !isset($user->height) || !isset($user->age)) {
            return 2000; // Default value
        }

        // Harris-Benedict Formula example
        if (isset($user->gender) && $user->gender == 'male') {
            $bmr = 88.362 + (13.397 * $user->weight) + (4.799 * $user->height) - (5.677 * $user->age);
        } else {
            $bmr = 447.593 + (9.247 * $user->weight) + (3.098 * $user->height) - (4.330 * $user->age);
        }

        // Activity factor (adjust based on user's activity level)
        $activityFactor = 1.5; // Moderate activity

        return round($bmr * $activityFactor);
    }
}