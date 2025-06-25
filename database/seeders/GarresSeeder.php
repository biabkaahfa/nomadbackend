<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GarresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('garres')->insert([
        [
            'name' => 'Gare Centrale',
            'localisation' => 'Ouagadougou-cissin',
            'ville' => 'Ouaga',
            'idCompagnie' => 1
        ],
        [
            'name' => 'Gare sud est',
            'localisation' => 'Ouagadougou-karpala',
            'ville' => 'Ouaga',
            'idCompagnie' => 1
        ]
    ]);
    }
}
