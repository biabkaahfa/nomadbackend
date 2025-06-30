<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TicketsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
         DB::table('tickets')->insert([
            [
            'dateReservation' => Carbon::now(),
            'statut' => 'CONFIRME',
            'idUtilisateur' => 1, // achat en ligne
            'idVoyage' => 1,
            'idGarre' => 1,
            'dateScan' => Carbon::now()->addDays(1),
            'idPaiement' => 1,
            'typeAchat' => 'en_ligne',
            'modeReception' => 'application',
            'email' => null,
            'name' => null,
            'telephone' => null,
            ],
            [
        'dateReservation' => Carbon::now(),
        'statut' => 'CONFIRME',
        'idUtilisateur' => null, // achat sur place
        'idVoyage' => 1,
        'idGarre' => 2,
        'dateScan' => Carbon::now()->addDays(2),
        'idPaiement' => 2,
        'typeAchat' => 'sur_place',
        'modeReception' => 'email',
        'email' => 'client@example.com',
        'name' => 'Jean Kaboré',
        'telephone' => '70123456',
    ]
        ]);
    }
}
