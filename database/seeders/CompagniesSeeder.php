<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompagniesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('compagnies')->insert([
            [
                'name' => 'TransFaso',
                'logo' => 'transfaso.png',
                'type' => 'PRIVE',
                'description' => 'Transport sous-regional national',
                'telephone' => '70112233',
                'email' => 'contact@transfaso.com',
                //'idAbonement' => 1, // Sera mis à jour après création
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'SOTRACO',
                'logo' => 'sotraco.png',
                'type' => 'PUBLIC',
                'description' => 'Transport urbain',
                'telephone' => '50112233',
                'email' => 'contact@sotraco.com',
           //     'idAbonement' => 2, // Sera mis à jour après création
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
