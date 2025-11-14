<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TicketsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tickets = [
            [
                'dateReservation' => Carbon::now(),
                'statut' => 'CONFIRME',
                'idUtilisateur' => 1,
                'idVoyage' => 1,
                'idGarre' => 1,
                'dateScan' => Carbon::now()->addDays(1),
                'idPaiement' => 1,
                'typeAchat' => 'En_ligne',
                'modeReception' => 'application',
                'email' => null,
                'name' => 'Alice Koné',
                'telephone' => '70123458',
                'namePersonneAPrevenir' => 'Bob Koné',
                'numeroPersonneAPrevenir' => '70123459',
                'emailPersonneAPrevenir' => 'bob@example.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'dateReservation' => Carbon::now(),
                'statut' => 'CONFIRME',
                'idUtilisateur' => null,
                'idVoyage' => 1,
                'idGarre' => 2,
                'dateScan' => Carbon::now()->addDays(2),
                'idPaiement' => 2,
                'typeAchat' => 'sur_place',
                'modeReception' => 'email',
                'email' => 'client@example.com',
                'name' => 'Jean Kaboré',
                'telephone' => '70123456',
                'namePersonneAPrevenir' => 'Marie Kaboré',
                'numeroPersonneAPrevenir' => '70123457',
                'emailPersonneAPrevenir' => 'marie@example.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        foreach ($tickets as &$ticket) {
            // Générer un QR code simple pour le seeder
            $qrData = [
                'ticket_name' => $ticket['name'] ?? 'Ticket',
                'date_reservation' => $ticket['dateReservation'],
                'statut' => $ticket['statut'],
            ];

            try {

                $ticket['qr_code_version'] = 'seeder_v1';
            } catch (\Exception $e) {
                
                $ticket['qr_code_version'] = 'error';
            }
        }

        DB::table('tickets')->insert($tickets);
    }
}
