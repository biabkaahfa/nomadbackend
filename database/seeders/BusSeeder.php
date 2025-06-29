<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('buses')->insert([
            [    'numeroBus' => 20,
                'nombrePlaces' => 60,
                'nombrePlaceDispo' => 60,
                'idCompagnie'=> 1,
                'status' => 'Actif',
                //'nombrePlaceDispo' => 60
            ]
        ]);
    }
}
