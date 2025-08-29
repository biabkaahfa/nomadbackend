<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeAbonementSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('type_abonements')->insert([
            [
                'nom' => 'Premium',
                'taux' => 15.5,
                'prix' => 150000,
                'maxTicket' => 500,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nom' => 'Standard',
                'taux' => 10.0,
                'prix' => 100000,
                'maxTicket' => 300,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nom' => 'Basique',
                'taux' => 5.0,
                'prix' => 50000,
                'maxTicket' => 100,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
