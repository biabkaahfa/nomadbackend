<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FrequenceTrajetsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('frequence_trajets')->insert([
            [
                'idTrajet' => 1, // doit correspondre à un trajet existant
                'heureDepart' => '07:30:00',
                'jourSemaine' => 'LUNDI',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idTrajet' => 1,
                'heureDepart' => '14:00:00',
                'jourSemaine' => 'VENDREDI',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
