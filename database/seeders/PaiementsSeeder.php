<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaiementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('paiements')->insert([
            [
                'montant' => 5000,
                'datePaiement' => Carbon::now(),
                'moyenPaiement' => 'OM',
                'statut' => 'SUCCES',
                'typeSource' => 'MOBILE',
                'idUtilisateur' => 1,
                'referenceTransaction' => 'TX12345678'
            ], [
                'montant' => 5000,
                'datePaiement' => Carbon::now(),
                'moyenPaiement' => 'OM',
                'statut' => 'SUCCES',
                'typeSource' => 'MOBILE',
                'idUtilisateur' => 1,
                'referenceTransaction' => 'BX12345678'
            ]
        ]);
    }
}
