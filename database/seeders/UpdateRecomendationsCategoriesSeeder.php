<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateRecomendationsCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Array contoh kategori untuk setiap makanan
        $categories = [
            'Vegetarian',
            'High Protein',
            'Low Carb',
            'Quick to Prepare',
            'Budget-Friendly'
        ];

        // Update data yang sudah ada dengan kategori random
        $recomendations = DB::table('recomendations')->get();

        foreach ($recomendations as $recommendation) {
            $randomCategory = $categories[array_rand($categories)];

            DB::table('recomendations')
                ->where('id', $recommendation->id)
                ->update(['category' => $randomCategory]);
        }

        // Atau jika Anda ingin assign kategori secara manual berdasarkan nama makanan:
        /*
        $manualCategories = [
            'Salad' => 'Vegetarian',
            'Chicken' => 'High Protein',
            'Beef' => 'High Protein',
            'Fish' => 'High Protein',
            'Vegetable' => 'Vegetarian',
            'Quinoa' => 'Low Carb',
            'Rice' => 'Quick to Prepare',
            // tambahkan mapping lainnya
        ];

        foreach ($recomendations as $recommendation) {
            $category = 'Quick to Prepare'; // default
            
            foreach ($manualCategories as $keyword => $cat) {
                if (stripos($recommendation->name, $keyword) !== false) {
                    $category = $cat;
                    break;
                }
            }
            
            DB::table('recomendations')
                ->where('id', $recommendation->id)
                ->update(['category' => $category]);
        }
        */
    }
}
