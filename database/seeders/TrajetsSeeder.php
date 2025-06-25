<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrajetsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('trajets')->insert([
            [
                'pointDepart' => 'Ouagadougou',
                'pointArrive' => 'Bobo-Dioulasso',
                'prix' => 5000,
                'status' => 'actif',
                'idCompagnie' => 1
            ]
        ]);
    }
}
