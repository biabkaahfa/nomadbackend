<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeAbonementSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            // Abonnements pour compagnies privées
            [
                'nom' => 'freemium',
                'libelle' => 'Freemium',
                'prix_mensuel' => 0,
                'limite_notifications' => 5,
                'acces_notes' => false,
                'commission_sur_place' => 20.00, // 20 francs par ticket
                'commission_en_ligne' => 8.00,   // 8% de commission
                'type_compagnie' => 'privee',
                'description' => 'Abonnement gratuit avec limitations',
                'est_actif' => true,
            ],
            [
                'nom' => 'standard',
                'libelle' => 'Standard',
                'prix_mensuel' => 300000.00,
                'limite_notifications' => 20,
                'acces_notes' => true,
                'commission_sur_place' => 0.00,   // 0 francs
                'commission_en_ligne' => 5.00,    // 5% de commission
                'type_compagnie' => 'privee',
                'description' => 'Abonnement standard avec fonctionnalités étendues',
                'est_actif' => true,
            ],
            [
                'nom' => 'premium',
                'libelle' => 'Premium',
                'prix_mensuel' => 500000.00,
                'limite_notifications' => null,   // Illimité
                'acces_notes' => true,
                'commission_sur_place' => 0.00,   // 0 francs
                'commission_en_ligne' => 3.00,    // 3% de commission
                'type_compagnie' => 'privee',
                'description' => 'Abonnement premium avec toutes les fonctionnalités',
                'est_actif' => true,
            ],
            // Abonnement pour compagnies publiques
            [
                'nom' => 'public',
                'libelle' => 'Public',
                'prix_mensuel' => 0,
                'limite_notifications' => null,   // Illimité
                'acces_notes' => true,
                'commission_sur_place' => 0.00,
                'commission_en_ligne' => 2.00,    // 2% pour les tickets en ligne
                'type_compagnie' => 'publique',
                'description' => 'Abonnement gratuit pour compagnies publiques',
                'est_actif' => true,
            ],
        ];

        DB::table('type_abonements')->insert($types);
    }
}
