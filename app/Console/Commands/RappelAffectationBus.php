<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Voyages;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RappelAffectationBus extends Command
{
    protected $signature = 'voyages:rappel-bus';
    protected $description = 'Rappelle les voyages sans bus affecté à 5 jours du départ';

    public function handle()
    {
        $dateCible = Carbon::today()->addDays(5);

        $voyages = Voyages::whereNull('idBus')
            ->whereDate('dateDepart', $dateCible)
            ->with('trajet.compagnie')
            ->get();

        if ($voyages->isEmpty()) {
            $this->info("✅ Aucun voyage sans bus pour le {$dateCible->toDateString()}.");
            return;
        }

        $this->warn("🚨 Voyages sans bus à affecter pour le {$dateCible->toDateString()}:");

        foreach ($voyages as $voyage) {
            $this->line("Compagnie: {$voyage->trajet->compagnie->nom} | Trajet ID: {$voyage->idTrajet} | Heure: {$voyage->heuresDepart}");
            // Ici tu peux ajouter envoi email ou notification si besoin
        }
    }
}
