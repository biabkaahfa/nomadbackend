<?php

// Fichier: app/Console/Commands/GererExpirationAbonnements.php
namespace App\Console\Commands;

use App\Models\Abonement;
use App\Models\TypeAbonement;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GererExpirationAbonnements extends Command
{
    protected $signature = 'abonnements:gerer-expiration';
    protected $description = 'Gère l\'expiration des abonnements et bascule vers freemium';

    public function handle()
    {
        // Récupérer l'ID du forfait freemium (à configurer)
        $freemiumId = TypeAbonement::where('nom', 'freemium')->value('id');

        if (!$freemiumId) {
            $this->error('Forfait freemium non trouvé');
            return;
        }

        // Trouver les abonnements payants expirés
        $abonnementsExpires = Abonement::where('statut', 'actif')
            ->where('dateFin', '<', now())
            ->whereHas('typeAbonement', function($query) {
                $query->where('prix', '>', 0); // Abonnements payants seulement
            })
            ->get();

        $count = 0;

        foreach ($abonnementsExpires as $abonement) {
            // Basculer vers freemium
            $this->basculerVersFreemium($abonement, $freemiumId);
            $count++;
        }

        $this->info("{$count} abonnement(s) basculé(s) vers freemium");
    }

    private function basculerVersFreemium(Abonement $abonement, $freemiumId)
    {
        // Historiser l'ancien abonnement (optionnel)
        $this->creerHistorique($abonement);

        // Mettre à jour l'abonnement vers freemium
        $abonement->update([
            'idTypeAbonement' => $freemiumId,
            'statut' => 'actif',
            'dateDebut' => now(),
            'dateFin' => now()->addYear(10), // Longue durée pour freemium
        ]);

        // Créer une notification
        $this->creerNotificationFreemium($abonement);
    }

    private function creerHistorique(Abonement $abonement)
    {
        // Optionnel: créer un historique des changements d'abonnement
        // Vous pouvez créer une table 'historique_abonnements' pour ça
    }

    private function creerNotificationFreemium(Abonement $abonement)
    {
        // Créer une notification pour informer l'utilisateur
        \App\Models\Message::create([
            'idAbonement' => $abonement->id,
            'type' => 'basculerFreemium',
            'contenu' => 'Votre abonnement payant a expiré. Vous avez été basculé automatiquement vers le forfait freemium.',
            'estVu' => false,
            'dateEnvoi' => now()
        ]);
    }
}
