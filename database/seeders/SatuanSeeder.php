<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SatuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $satuan = [
            [1, 'gram'],
            [2, 'buah'],
            [3, 'lembar'],
            [4, 'potong'],
            [5, 'sendok makan'],
            [6, 'sendok teh'],
            [7, 'gelas'],
            [8, 'mangkuk'],
            [9, 'biji'],
            [10, 'ikat'],
            [11, 'batang'],
            [12, 'butir'],
            [13, 'siung'],
            [14, 'helai'],
            [15, 'tangkai'],
        ];

        foreach ($satuan as $satu) {
            DB::table('satuan')->insert([
                'id_satuan' => $satu[0],
                'nama_satuan' => $satu[1],
            ]);
        }
    }
}
