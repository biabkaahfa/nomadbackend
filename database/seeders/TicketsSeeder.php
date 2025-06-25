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
                'idUtilisateur' => 1,
                'idVoyage' => 1,
                'idGarre' => 1,
                'dateScan' => Carbon::now()->addDays(1),
                'idPaiement' => 1
            ]
        ]);
    }
}
