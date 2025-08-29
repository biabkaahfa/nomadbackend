<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ParametresSeeder extends Seeder
{
    public function run(): void
    {
        // On récupère les ID des compagnies
        $transfaso = DB::table('compagnies')->where('name', 'TransFaso')->first();
        $sotraco = DB::table('compagnies')->where('name', 'SOTRACO')->first();

        if ($transfaso) {
            DB::table('parametres')->updateOrInsert(
                ['idCompagnie' => $transfaso->id],
                [
                    'logo' => 'transfaso-param-logo.png',
                    'couleur_principale' => '#0066CC',
                    'couleur_secondaire' => '#FF9900',
                    'slogan' => 'Voyageons ensemble avec confiance',
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        if ($sotraco) {
            DB::table('parametres')->updateOrInsert(
                ['idCompagnie' => $sotraco->id],
                [
                    'logo' => 'sotraco-param-logo.png',
                    'couleur_principale' => '#228B22',
                    'couleur_secondaire' => '#FFD700',
                    'slogan' => 'La ville en mouvement',
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        // Paramètres globaux pour l'Admin général
        DB::table('parametres')->updateOrInsert(
            ['idCompagnie' => null],
            [
                'logo' => 'logos/default-admin.png',
                'couleur_principale' => '#0066CC',
                'couleur_secondaire' => '#FF9900',
                'slogan' => 'Bienvenue sur notre plateforme',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}
