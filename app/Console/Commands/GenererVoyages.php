<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Compagnies;
use App\Models\Voyages;
use App\Models\Trajets;
use Carbon\Carbon;

class GenererVoyages extends Command
{
    protected $signature = 'voyages:generer';
    protected $description = 'Génère automatiquement les voyages pour les 30 prochains jours';

    public function handle()
    {
        $today = Carbon::today();
        $endDate = $today->copy()->addDays(30);

        $compagnies = Compagnies::with(['trajets.frequences'])->get();

        foreach ($compagnies as $compagnie) {
            foreach ($compagnie->trajets as $trajet) {
                foreach ($trajet->frequences as $frequence) {

                    for ($date = $today->copy(); $date->lte($endDate); $date->addDay()) {
                        $jourSemaine = strtoupper($date->locale('fr')->isoFormat('dddd'));

                        if ($frequence->jourSemaine === 'CHAQUEJOURS' || $frequence->jourSemaine === $jourSemaine) {

                            // Vérifie l'existence
                            $existe = Voyages::where('idTrajet', $trajet->id)
                                ->whereDate('dateDepart', $date)
                                ->whereTime('heuresDepart', $frequence->heureDepart)
                                ->exists();

                            if (!$existe) {
                                Voyages::create([
                                    'idTrajet' => $trajet->id,
                                    'heuresDepart' => $frequence->heureDepart,
                                    'dateDepart' => $date->toDateString(),
                                    'idBus' => null
                                ]);

                                $this->info("✅ Voyage créé : {$compagnie->name} | Trajet ID {$trajet->id} | {$date->toDateString()} à {$frequence->heureDepart}");
                            }
                        }
                    }
                }
            }
        }

        $this->info("✅ Tous les voyages ont été générés.");
    }
}
