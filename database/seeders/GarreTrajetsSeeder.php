<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GarreTrajetsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
         DB::table('garre_trajets')->insert([
            ['idGarre' => 1, 'idTrajet' => 1],
            ['idGarre' => 2, 'idTrajet' => 1], // s’il y a plusieurs gares dans le trajet
        ]);
    }
}
