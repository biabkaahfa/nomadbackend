<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AbonementSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $dateDebut = $now->copy()->startOfMonth();
        $dateFin = $now->copy()->addMonth()->startOfMonth();

        DB::table('abonements')->insert([
            [
                'idTypeAbonement' => 1, // Premium
                'idCompagnie' => 2,
                'duree' => 30,
                'dateDebut' => $dateDebut,
                'dateFin' => $dateFin,
                'statut' => 'actif',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'idTypeAbonement' => 2, // Standard
                'idCompagnie' => 1,
                'duree' => 30,
                'dateDebut' => $dateDebut,
                'dateFin' => $dateFin,
                'statut' => 'actif',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);

        // Mise à jour des compagnies avec les idAbonement
        // DB::table('compagnies')
        //     ->where('name', 'TransFaso')
        //     ->update(['idAbonement' => 1]);

        // DB::table('compagnies')
        //     ->where('name', 'SOTRACO')
        //     ->update(['idAbonement' => 2]);
    }
}
