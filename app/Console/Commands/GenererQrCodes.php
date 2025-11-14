<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ticket;
use Illuminate\Support\Facades\Log;

class GenererQrCodes extends Command
{
    protected $signature = 'tickets:generer-qrcodes';
    protected $description = 'Génère les QR codes pour tous les tickets qui n\'en ont pas';

    public function handle()
    {
        // Charger les relations nécessaires pour éviter les requêtes N+1
        $ticketsSansQrCode = Ticket::whereNull('qr_code_base64')
            ->with(['voyage.trajet.compagnie', 'paiement'])
            ->get();

        $this->info("📋 Génération de QR codes pour {$ticketsSansQrCode->count()} tickets...");

        if ($ticketsSansQrCode->count() === 0) {
            $this->info("✅ Tous les tickets ont déjà un QR code !");
            return Command::SUCCESS;
        }

        $success = 0;
        $errors = 0;

        $bar = $this->output->createProgressBar($ticketsSansQrCode->count());

        foreach ($ticketsSansQrCode as $ticket) {
            try {
                if ($ticket->genererQrCode()) {
                    $success++;
                } else {
                    $errors++;
                    $this->error("❌ Erreur avec le ticket ID: {$ticket->id}");
                }
            } catch (\Exception $e) {
                $errors++;
                Log::error("Erreur critique avec ticket {$ticket->id}: " . $e->getMessage());
                $this->error("💥 Erreur critique avec ticket ID: {$ticket->id}");
            }

            $bar->advance();

            // Petite pause pour éviter la surcharge
            usleep(100000); // 100ms
        }

        $bar->finish();

        $this->newLine();
        $this->info("✅ Génération terminée !");
        $this->info("📊 Résultat:");
        $this->info("   - ✅ Succès: {$success}");
        $this->info("   - ❌ Erreurs: {$errors}");

        if ($errors > 0) {
            $this->warn("⚠️  Certains tickets ont eu des erreurs. Consultez les logs.");
        }

        return Command::SUCCESS;
    }
}
