<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MessagesSeeder extends Seeder
{
    public function run()
    {
        DB::table('messages')->insert([
            [
                'idAbonement' => 1,  // Doit correspondre à un ID existant dans la table abonements
                'type' => 'rappelTicket',
                'contenu' => 'Il vous reste seulement 10 tickets disponibles ce mois-ci',
                'dateEnvoi' => Carbon::now()->subDays(2),
                'estVu' => false,
                'dateLecture' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'idAbonement' => 2,  // Doit correspondre à un ID existant dans la table abonements
                'type' => 'rappelFin',
                'contenu' => 'Votre abonement expire dans 5 jours',
                'dateEnvoi' => Carbon::now()->subDays(1),
                'estVu' => true,
                'dateLecture' => Carbon::now()->subHours(3),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'idAbonement' => 1,
                'type' => 'notification',
                'contenu' => 'Nouvelle fonctionnalité disponible dans votre espace abonnement',
                'dateEnvoi' => Carbon::now(),
                'estVu' => false,
                'dateLecture' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ]);
    }
}
