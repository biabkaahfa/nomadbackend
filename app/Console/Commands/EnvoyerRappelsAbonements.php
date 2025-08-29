<?php

// Fichier: app/Console/Commands/EnvoyerRappelsAbonements.php
namespace App\Console\Commands;

use App\Models\Abonement;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Console\Command;


class EnvoyerRappelsAbonements extends Command
{
    protected $signature = 'rappels:envoyer';
    protected $description = 'Générer les notifications pour les abonnements';

    public function handle()
    {
        // Rappels pour fin d'abonnement (5 derniers jours)
        Abonement::where('statut', 'actif')
            ->whereBetween('dateFin', [
                now(),
                now()->addDays(5)
            ])
            ->each(function($abonement) {
                $joursRestants = now()->diffInDays($abonement->dateFin);

                $this->creerNotification(
                    $abonement,
                    'rappelFin',
                    "Votre abonement expire dans {$joursRestants} jour(s) - Renouvelez maintenant!"
                );
            });

        // Rappels pour tickets (quand il reste ≤ 10 tickets)
        Abonement::with(['typeAbonement', 'tickets' => function($q) {
                $q->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
            }])
            ->whereHas('typeAbonement', fn($q) => $q->where('maxTicket', '>', 0))
            ->each(function($abonement) {
                $ticketsUtilises = $abonement->tickets->count();
                $ticketsRestants = $abonement->typeAbonement->maxTicket - $ticketsUtilises;

                if ($ticketsRestants <= 10 && $ticketsRestants > 0) {
                    $this->creerNotification(
                        $abonement,
                        'rappelTicket',
                        "Attention! Il ne vous reste que {$ticketsRestants} ticket(s) pour ce mois."
                    );
                }
            });

        $this->info('Notifications générées avec succès');
    }

    private function creerNotification($abonement, $type, $contenu)
    {
        // On évite les doublons
        Message::firstOrCreate([
            'idAbonement' => $abonement->id,
            'type' => $type,
            'contenu' => $contenu,
            'dateEnvoi' => now()->startOfDay() // Pour éviter les doublons dans la journée
        ], [
            'estVu' => false
        ]);
    }
}
