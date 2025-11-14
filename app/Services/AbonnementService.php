<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Ticket;
use App\Models\Abonement;
use App\Models\Compagnies;
use App\Models\Notification;
use App\Models\Notifications;
use App\Models\TypeAbonement;

class AbonnementService
{
    /**
     * Vérifie si une compagnie peut envoyer une notification
     */
    public function peutEnvoyerNotification(Compagnies $compagnie): bool
    {
        $abonnement = $compagnie->abonnementActif;

        if (!$abonnement) {
            return false;
        }

        $typeAbonement = $abonnement->typeAbonement;

        // Vérifier si les notifications sont illimitées
        if ($typeAbonement->notificationsIllimitees()) {
            return true;
        }

        // Compter les notifications du jour
        $notificationsAujourdhui = Notifications::where('idCompagnie', $compagnie->id)
            ->whereDate('created_at', Carbon::today())
            ->count();

        return $notificationsAujourdhui < $typeAbonement->maxTicket;
    }

    /**
     * Calcule la commission pour un ticket
     */
    public function calculerCommission(Ticket $ticket): float
    {
        $compagnie = $ticket->voyage->trajet->compagnie;
        $abonnement = $compagnie->abonnementActif;

        if (!$abonnement) {
            return 0;
        }

        $typeAbonement = $abonnement->typeAbonement;

        if ($ticket->typeAchat === 'sur_place') {
            return $typeAbonement->calculerCommissionSurPlace();
        } elseif ($ticket->typeAchat === 'en_ligne') {
            return $typeAbonement->calculerCommissionEnLigne($ticket->prix);
        }

        return 0;
    }

    /**
     * Vérifie l'accès aux notes
     */
    public function aAccesAuxNotes(Compagnies $compagnie): bool
    {
        $abonnement = $compagnie->abonnementActif;

        if (!$abonnement) {
            return false;
        }

        return $abonnement->typeAbonement->acces_notes;
    }

    /**
     * Obtient le nombre de notifications restantes aujourd'hui
     */
    public function notificationsRestantesAujourdhui(Compagnies $compagnie): ?int
    {
        $abonnement = $compagnie->abonnementActif;

        if (!$abonnement) {
            return 0;
        }

        $typeAbonement = $abonnement->typeAbonement;

        if ($typeAbonement->notificationsIllimitees()) {
            return null; // Illimité
        }

        $notificationsAujourdhui = Notifications::where('idCompagnie', $compagnie->id)
            ->whereDate('created_at', Carbon::today())
            ->count();

        return max(0, $typeAbonement->maxTicket - $notificationsAujourdhui);
    }

    /**
     * Crée un nouvel abonnement
     */
    public function creerAbonnement(Compagnies $compagnie, TypeAbonement $typeAbonement): Abonement
    {
        // Désactiver les anciens abonnements
        Abonement::where('idCompagnie', $compagnie->id)
            ->where('statut', 'actif')
            ->update(['statut' => 'inactif']);

        // Créer le nouvel abonnement
        return Abonement::create([
            'idTypeAbonement' => $typeAbonement->id,
            'idCompagnie' => $compagnie->id,
            'dateDebut' => Carbon::now(),
            'dateFin' => Carbon::now()->addDays(30),
            'statut' => 'actif',
        ]);
    }

    /**
     * Vérifie et met à jour le statut des abonnements expirés
     */
    public function verifierAbonnementsExpires(): void
    {
        Abonement::where('statut', 'actif')
            ->where('dateFin', '<', Carbon::now())
            ->update(['statut' => 'expire']);
    }
}
