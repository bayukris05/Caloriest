<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Recomendations;
use App\Models\Menu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class RecomendationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function getCategory($name)
    {
        $name = strtolower($name);
        if (preg_match('/(nasi|bubur|lontong|kentang|ubi|singkong|roti)/i', $name)) {
            return 'Makanan Pokok';
        } elseif (preg_match('/(ayam|daging|sapi|ikan|lele|telur|udang|cumi|sate|rendang|bebek)/i', $name)) {
            return 'Lauk Pauk';
        } elseif (preg_match('/(sayur|tumis|bayam|kangkung|brokoli|tomat|kubis|timun|wortel|buncis|kacang|tauge)/i', $name)) {
            return 'Sayuran';
        } elseif (preg_match('/(mie|bihun|kwetiau|pasta|spaghetti)/i', $name)) {
            return 'Mie & Pasta';
        } elseif (preg_match('/(tahu|tempe|jamur|perkedel|bakwan)/i', $name)) {
            return 'Lauk Nabati';
        } elseif (preg_match('/(soto|sop|bakso|gulai|rawon|kari)/i', $name)) {
            return 'Makanan Berkuah';
        } elseif (preg_match('/(apel|jeruk|pisang|mangga|anggur|semangka|melon|pepaya|salak|duku|alpukat)/i', $name)) {
            return 'Buah-buahan';
        }
        return 'Lainnya';
    }

    private function getImagePath($name)
    {
        $snakeName = str_replace(' ', '_', strtolower($name));
        
        if (file_exists(public_path('images/makanan/' . $snakeName . '.png'))) {
            return 'images/makanan/' . $snakeName . '.png';
        } elseif (file_exists(public_path('images/makanan/' . $snakeName . '.jpg'))) {
            return 'images/makanan/' . $snakeName . '.jpg';
        }
        return null;
    }

    private function transformItem($item)
    {
        $category = $this->getCategory($item->nama_menu);
        $imagePath = $this->getImagePath($item->nama_menu);

        return (object) [
            'id' => $item->id_menu,
            'name' => $item->nama_menu,
            'description' => 'Kalori per ' . $item->jumlah . 'g/ml' . ($item->keterangan ? '. ' . $item->keterangan : ''),
            'calorie_range' => $item->jumlah_kalori . ' Kcal',
            'raw_calories' => $item->jumlah_kalori, 
            'image_path' => $imagePath, 
            'image_color' => '#' . substr(md5($item->nama_menu), 0, 6),
            'category' => $category
        ];
    }

    public function showRecommendations(Request $request)
    {
        // Jika request AJAX untuk mengambil menu user
        if ($request->wantsJson() && !$request->has('filter')) {
            try {
                Log::info('AJAX request received for user menus', [
                    'user_id' => Auth::id(),
                    'headers' => $request->headers->all()
                ]);

                $menus = Auth::user()->menus()
                    ->select('id', 'name', 'calories')
                    ->latest()
                    ->get();

                Log::info('Menus found:', ['count' => $menus->count(), 'data' => $menus->toArray()]);

                $formattedMenus = $menus->map(function ($menu) {
                    $calories = is_numeric($menu->calories) ? $menu->calories : preg_replace('/[^0-9]/', '', $menu->calories);
                    return [
                        'id' => $menu->id,
                        'name' => $menu->name,
                        'calories' => $calories ?: 0
                    ];
                });

                return response()->json($formattedMenus);
            } catch (Exception $e) {
                Log::error('Failed to load menus: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json(['error' => 'Failed to load menus'], 500);
            }
        }

        // Jika request AJAX untuk filter recommendations
        if ($request->wantsJson() && $request->has('filter')) {
            try {
                $category = $request->get('filter');
                $search = $request->get('search');
                
                Log::info('Filter recommendations', ['category' => $category, 'search' => $search]);

                $query = \App\Models\MasterMenu::query();

                // Filter by search (using nama_menu)
                if ($search) {
                    $query->where('nama_menu', 'LIKE', '%' . $search . '%');
                }

                $items = $query->get();

                // Helper transformation for consistency
                $allRecommendations = $items->map(function($item) {
                    return $this->transformItem($item);
                });

                // Filter by category if requested
                if ($category && $category !== 'all') {
                    $allRecommendations = $allRecommendations->where('category', $category);
                }

                // Sort: Items with images first
                $allRecommendations = $allRecommendations->sortByDesc(function($item) {
                    return $item->image_path ? 1 : 0;
                })->values();

                // Flatten the pages for grid layout - standard 4-col grid doesn't strictly need chunking 
                // but if the view expects chunks, we keep chunks.
                $pageRows = $allRecommendations->chunk(4)->map(function($chunk) {
                    return $chunk->values();
                });

                return response()->json([
                    'success' => true,
                    'data' => $pageRows,
                    'total' => $allRecommendations->count()
                ]);
            } catch (Exception $e) {
                Log::error('Failed to filter recommendations: ' . $e->getMessage());
                return response()->json(['error' => 'Failed to filter recommendations: ' . $e->getMessage()], 500);
            }
        }

        // Tampilan normal untuk halaman rekomendasi
        $items = \App\Models\MasterMenu::all();
        
        $recommendations = $items->map(function($item) {
            return $this->transformItem($item);
        });
        
        // Sort: Items with images first
        $recommendations = $recommendations->sortByDesc(function($item) {
            return $item->image_path ? 1 : 0;
        })->values();

        $pageRows = $recommendations->chunk(4)->map(function($chunk) {
            return $chunk->values();
        });

        // Get unique categories for dropdown
        $categories = $recommendations->pluck('category')->unique()->sort()->values();

        return view('auth.recomend', compact('pageRows', 'categories'));
    }

    public function destroyMenu($id)
    {
        try {
            Log::info('Attempting to delete menu (manual lookup)', ['requested_id' => $id, 'user_id' => Auth::id()]);

            $menu = Menu::find($id);

            if (!$menu) {
                Log::warning('Menu not found during manual lookup', ['requested_id' => $id]);
                return response()->json(['error' => 'Menu not found'], 404);
            }

            if ($menu->users_id !== Auth::id()) {
                Log::warning('Unauthorized delete attempt', ['menu_id' => $menu->id, 'user_id' => Auth::id()]);
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $menu->delete();
            Log::info('Menu deleted successfully', ['menu_id' => $menu->id]);
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            Log::error('Failed to delete menu: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete menu'], 500);
        }
    }

    public function storeMenu(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'calories' => 'required', // Accept string '400' or '400 Kcal'
            ]);

            // Clean calories to get just number if needed, but DB is string so safe to store as is
            // However, it's better to store just the number if we want to calculate later.
            // But let's check what existing data looks like. 
            // In showRecommendations: $menus->select('id', 'name', 'calories') -> formattedMenus uses calories.
            // User menu list shows "$menu->calories Kcal" in JS.
            // So if we store "400", JS shows "400 Kcal".
            // If we store "400 Kcal", JS shows "400 Kcal Kcal".
            // Let's strip non-numeric just in case.
            $calories = preg_replace('/[^0-9]/', '', $validated['calories']);
            
            $menu = Auth::user()->menus()->create([
                'name' => $validated['name'],
                'calories' => $calories
            ]);

            return response()->json(['success' => true, 'data' => $menu]);

        } catch (Exception $e) {
            Log::error('Failed to store menu: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to store menu: ' . $e->getMessage()], 500);
        }
    }
}
