<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Recomendations;

class RecommendationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recomendations = [
            [
                'name' => 'Nasi Goreng Spesial',
                'description' => 'Nasi goreng Indonesia dengan bumbu spesial...',
                'calorie_range' => '400-500 kkal',
                'image_color' => '#ff6b6b,#ffe66d'
            ],

        ];

        foreach ($recomendations as $rec) {
            Recomendations::create($rec);
        }
    }
}
