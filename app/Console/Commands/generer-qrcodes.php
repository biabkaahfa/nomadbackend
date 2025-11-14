<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ticket;

class GenererQrCodesTickets extends Command
{
    protected $signature = 'tickets:generer-qrcodes';
    protected $description = 'Génère les QR codes pour tous les tickets qui n\'en ont pas';

    public function handle()
    {
        $ticketsSansQrCode = Ticket::whereNull('qr_code_base64')->get();

        $this->info("Génération de QR codes pour {$ticketsSansQrCode->count()} tickets...");

        $bar = $this->output->createProgressBar($ticketsSansQrCode->count());

        foreach ($ticketsSansQrCode as $ticket) {
            $ticket->genererQrCode();
            $bar->advance();
        }

        $bar->finish();
        $this->info("\n✅ Génération terminée !");
    }
}
