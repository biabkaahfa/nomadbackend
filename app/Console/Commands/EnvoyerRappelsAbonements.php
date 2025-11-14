<?php

namespace App\Console\Commands;

use App\Models\Abonement;
use App\Models\AbonementPublic;
use App\Models\Ticket;
use App\Models\Voyages;
use App\Models\Message;
use App\Services\FirebaseService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class EnvoyerNotificationsSysteme extends Command
{
    protected $signature = 'notifications:envoyer';
    protected $description = 'Envoyer toutes les notifications système (abonnements, voyages, notations, incidents)';

    public function handle()
    {
        $firebaseService = new FirebaseService();

        $this->info('🚀 Début de l\'envoi des notifications système...');

        // 1. Gestion des expirations d'abonnements professionnels (UNIQUEMENT messages internes)
        $this->gererExpirationsAbonnementsPro();

        // 2. Rappels pour abonnements publics (PUSH MOBILE + messages internes)
        $this->rappelsAbonnementsPublics($firebaseService);

        // 3. Rappels pour voyages (veille et 5h avant) - PUSH MOBILE + messages internes
        $this->rappelsVoyages($firebaseService);

        // 4. Rappels de notation - PUSH MOBILE + messages internes
        $this->rappelsNotation($firebaseService);

        // 5. Notifications d'incidents (retards, annulations) - PUSH MOBILE + messages internes
        $this->notificationsIncidents($firebaseService);

        $this->info('✅ Toutes les notifications ont été traitées.');
    }

    /**
     * 1. Gestion des expirations d'abonnements professionnels
     * UNIQUEMENT MESSAGES INTERNES - PAS DE PUSH MOBILE
     */
    protected function gererExpirationsAbonnementsPro()
    {
        $this->info('📅 Gestion des expirations d\'abonnements professionnels...');

        // Abonnements professionnels expirant dans 5, 3 et 1 jour(s)
        $delaisRappels = [5, 3, 1];

        foreach ($delaisRappels as $jours) {
            Abonement::where('statut', 'actif')
                ->whereDate('dateFin', now()->addDays($jours))
                ->with(['compagnie.users'])
                ->each(function($abonement) use ($jours) {
                    $joursRestants = $jours;

                    foreach ($abonement->compagnie->users as $user) {
                        // ❌ PAS DE NOTIFICATION PUSH MOBILE pour les abonnements pro
                        // ✅ UNIQUEMENT MESSAGE INTERNE
                        $this->creerNotification(
                            $abonement->id,
                            'rappel_abonnement_pro',
                            "Votre abonnement professionnel expire dans {$joursRestants} jour(s) - Renouvelez maintenant!",
                            $user->id
                        );

                        $this->info("✅ INTERNE: Rappel abonnement pro ({$joursRestants}j) envoyé à {$user->email}");
                    }
                });
        }

        // Basculer les abonnements expirés vers freemium
        $this->call('abonnements:gerer-expiration');
    }

    /**
     * 2. Rappels pour abonnements publics
     * PUSH MOBILE + MESSAGES INTERNES
     */
    protected function rappelsAbonnementsPublics(FirebaseService $firebaseService)
    {
        $this->info('👥 Rappels pour abonnements publics...');

        // Abonnements publics expirant dans 7, 3 et 1 jour(s)
        $delaisRappels = [7, 3, 1];

        foreach ($delaisRappels as $jours) {
            AbonementPublic::where('statut', 'actif')
                ->whereDate('dateFin', now()->addDays($jours))
                ->with(['user'])
                ->each(function($abonementPublic) use ($firebaseService, $jours) {
                    if ($abonementPublic->user) {
                        // ✅ NOTIFICATION PUSH MOBILE
                        $resultPush = $firebaseService->sendToUser(
                            $abonementPublic->user,
                            '📅 Abonnement Public Expirant',
                            "Votre abonnement public expire dans {$jours} jour(s). Pensez à le renouveler !",
                            [
                                'type' => 'rappel_abonnement_public',
                                'jours_restants' => (string) $jours,
                                'abonement_public_id' => (string) $abonementPublic->id,
                                'screen' => 'abonnement_public',
                                'action' => 'renouveler_abonnement',
                            ]
                        );

                        // ✅ MESSAGE INTERNE
                        $this->creerNotification(
                            null,
                            'rappel_abonnement_public',
                            "Votre abonnement public expire dans {$jours} jour(s) - Pensez à le renouveler!",
                            $abonementPublic->idUser
                        );

                        if ($resultPush['success']) {
                            $this->info("✅ PUSH + INTERNE: Rappel abonnement public ({$jours}j) envoyé à l'utilisateur {$abonementPublic->idUser}");
                        } else {
                            $this->error("❌ PUSH ÉCHEC: User {$abonementPublic->idUser} - {$resultPush['error']}");
                        }
                    }
                });
        }
    }

    /**
     * 3. Rappels pour voyages
     * PUSH MOBILE + MESSAGES INTERNES
     */
    protected function rappelsVoyages(FirebaseService $firebaseService)
    {
        $this->info('🚗 Rappels pour voyages...');

        // RAPPEL LA VEILLE (24h avant)
        $this->rappelsVoyagesVeille($firebaseService);

        // RAPPEL 5 HEURES AVANT
        $this->rappelsVoyages5Heures($firebaseService);
    }

    protected function rappelsVoyagesVeille(FirebaseService $firebaseService)
    {
        $dateDemain = now()->addDay()->toDateString();

        Ticket::whereHas('voyage', function($query) use ($dateDemain) {
                $query->whereDate('dateDepart', $dateDemain);
            })
            ->with(['user', 'voyage.trajet'])
            ->each(function($ticket) use ($firebaseService) {
                if ($ticket->user) {
                    $voyage = $ticket->voyage;
                    $heureDepart = Carbon::parse($voyage->heuresDepart)->format('H:i');

                    // ✅ NOTIFICATION PUSH MOBILE
                    $resultPush = $firebaseService->sendToUser(
                        $ticket->user,
                        '📌 Rappel Voyage Demain',
                        "Votre voyage {$voyage->trajetComplet} départ demain à {$heureDepart}. Soyez à l'heure !",
                        [
                            'type' => 'rappel_voyage_veille',
                            'ticket_id' => (string) $ticket->id,
                            'voyage_id' => (string) $voyage->id,
                            'heure_depart' => $heureDepart,
                            'screen' => 'voyage_details',
                            'action' => 'voir_voyage',
                        ]
                    );

                    // ✅ MESSAGE INTERNE
                    $this->creerNotification(
                        null,
                        'rappel_voyage_veille',
                        "Rappel: Votre voyage {$voyage->trajetComplet} départ demain à {$heureDepart}",
                        $ticket->idUtilisateur,
                        $ticket->id
                    );

                    if ($resultPush['success']) {
                        $this->info("✅ PUSH + INTERNE: Rappel veille envoyé pour le ticket {$ticket->id}");
                    } else {
                        $this->error("❌ PUSH ÉCHEC: Ticket {$ticket->id} - {$resultPush['error']}");
                    }
                }
            });
    }

    protected function rappelsVoyages5Heures(FirebaseService $firebaseService)
    {
        $maintenant = now();
        $dans5Heures = now()->addHours(5);

        Ticket::whereHas('voyage', function($query) use ($maintenant, $dans5Heures) {
                $query->whereBetween('dateDepart', [
                    $maintenant->toDateTimeString(),
                    $dans5Heures->toDateTimeString()
                ]);
            })
            ->with(['user', 'voyage.trajet'])
            ->each(function($ticket) use ($firebaseService, $maintenant) {
                if ($ticket->user) {
                    $voyage = $ticket->voyage;
                    $heureDepart = Carbon::parse($voyage->heuresDepart)->format('H:i');
                    $tempsRestant = $maintenant->diffInHours(Carbon::parse($voyage->dateDepart . ' ' . $voyage->heuresDepart));

                    // ✅ NOTIFICATION PUSH MOBILE
                    $resultPush = $firebaseService->sendToUser(
                        $ticket->user,
                        '⏰ Départ dans ' . $tempsRestant . 'h',
                        "Votre voyage {$voyage->trajetComplet} départ à {$heureDepart}. Préparez-vous !",
                        [
                            'type' => 'rappel_voyage_5h',
                            'ticket_id' => (string) $ticket->id,
                            'voyage_id' => (string) $voyage->id,
                            'heure_depart' => $heureDepart,
                            'temps_restant' => (string) $tempsRestant,
                            'screen' => 'voyage_details',
                            'action' => 'voir_voyage',
                        ]
                    );

                    // ✅ MESSAGE INTERNE
                    $this->creerNotification(
                        null,
                        'rappel_voyage_5h',
                        "Départ dans {$tempsRestant}h: Voyage {$voyage->trajetComplet} à {$heureDepart}",
                        $ticket->idUtilisateur,
                        $ticket->id
                    );

                    if ($resultPush['success']) {
                        $this->info("✅ PUSH + INTERNE: Rappel 5h envoyé pour le ticket {$ticket->id}");
                    } else {
                        $this->error("❌ PUSH ÉCHEC: Ticket {$ticket->id} - {$resultPush['error']}");
                    }
                }
            });
    }

    /**
     * 4. Rappels de notation
     * PUSH MOBILE + MESSAGES INTERNES
     */
    protected function rappelsNotation(FirebaseService $firebaseService)
    {
        $this->info('⭐ Rappels de notation...');

        // Tickets utilisés il y a 1-3 jours sans notation
        Ticket::where('estUtilise', true)
            ->whereDate('dateUtilisation', '<=', now()->subDays(1))
            ->whereDate('dateUtilisation', '>=', now()->subDays(3))
            ->whereDoesntHave('note')
            ->with(['user', 'voyage.trajet'])
            ->each(function($ticket) use ($firebaseService) {
                if ($ticket->user) {
                    // ✅ NOTIFICATION PUSH MOBILE
                    $resultPush = $firebaseService->sendRappelNotation(
                        $ticket->user,
                        $ticket->voyage->trajetComplet,
                        $ticket->id
                    );

                    // ✅ MESSAGE INTERNE
                    $this->creerNotification(
                        null,
                        'rappel_notation',
                        "Comment s'est passé votre voyage {$ticket->voyage->trajetComplet} ? Donnez-nous votre avis !",
                        $ticket->idUtilisateur,
                        $ticket->id
                    );

                    if ($resultPush['success']) {
                        $this->info("✅ PUSH + INTERNE: Rappel notation envoyé pour le voyage {$ticket->voyage->trajetComplet}");
                    } else {
                        $this->error("❌ PUSH ÉCHEC: Voyage {$ticket->voyage->trajetComplet} - {$resultPush['error']}");
                    }
                }
            });
    }

    /**
     * 5. Notifications d'incidents
     * PUSH MOBILE + MESSAGES INTERNES
     */
    protected function notificationsIncidents(FirebaseService $firebaseService)
    {
        $this->info('🚨 Vérification des incidents...');

        // Voyages en retard (départ prévu il y a plus de 15 minutes mais non parti)
        $this->notificationsRetards($firebaseService);

        // Voyages annulés
        $this->notificationsAnnulations($firebaseService);

        // Autres incidents
        $this->notificationsAutresIncidents($firebaseService);
    }

    protected function notificationsRetards(FirebaseService $firebaseService)
    {
        $dateMaintenant = now();
        $retardLimite = now()->subMinutes(15);

        Voyages::where('dateDepart', '<=', $dateMaintenant->toDateString())
            ->where('heuresDepart', '<=', $retardLimite->format('H:i:s'))
            ->whereDoesntHave('tickets', function($query) {
                $query->whereNotNull('dateScan');
            })
            ->with(['tickets.user', 'trajet'])
            ->each(function($voyage) use ($firebaseService, $retardLimite) {
                $retardMinutes = $retardLimite->diffInMinutes(
                    Carbon::parse($voyage->dateDepart . ' ' . $voyage->heuresDepart)
                );

                foreach ($voyage->tickets as $ticket) {
                    if ($ticket->user) {
                        // ✅ NOTIFICATION PUSH MOBILE
                        $resultPush = $firebaseService->sendToUser(
                            $ticket->user,
                            '⚠️ Retard Annoncé',
                            "Votre voyage {$voyage->trajetComplet} est retardé de {$retardMinutes} minutes. Nous nous excusons pour ce contretemps.",
                            [
                                'type' => 'retard_voyage',
                                'ticket_id' => (string) $ticket->id,
                                'voyage_id' => (string) $voyage->id,
                                'retard_minutes' => (string) $retardMinutes,
                                'screen' => 'voyage_details',
                                'action' => 'voir_voyage',
                            ]
                        );

                        // ✅ MESSAGE INTERNE
                        $this->creerNotification(
                            null,
                            'retard_voyage',
                            "Retard: Votre voyage {$voyage->trajetComplet} est retardé de {$retardMinutes} minutes",
                            $ticket->idUtilisateur,
                            $ticket->id
                        );

                        if ($resultPush['success']) {
                            $this->info("✅ PUSH + INTERNE: Notification retard envoyée pour le ticket {$ticket->id}");
                        } else {
                            $this->error("❌ PUSH ÉCHEC: Ticket {$ticket->id} - {$resultPush['error']}");
                        }
                    }
                }
            });
    }

    protected function notificationsAnnulations(FirebaseService $firebaseService)
    {
        // Voyages avec statut 'annule'
        Voyages::where('statut', 'annule')
            ->where('dateDepart', '>=', now()->subDay()) // Annulations récentes
            ->with(['tickets.user', 'trajet'])
            ->each(function($voyage) use ($firebaseService) {
                foreach ($voyage->tickets as $ticket) {
                    if ($ticket->user) {
                        // ✅ NOTIFICATION PUSH MOBILE
                        $resultPush = $firebaseService->sendToUser(
                            $ticket->user,
                            '❌ Voyage Annulé',
                            "Votre voyage {$voyage->trajetComplet} a été annulé. Vous serez remboursé sous 24h.",
                            [
                                'type' => 'annulation_voyage',
                                'ticket_id' => (string) $ticket->id,
                                'voyage_id' => (string) $voyage->id,
                                'screen' => 'remboursement',
                                'action' => 'demander_remboursement',
                            ]
                        );

                        // ✅ MESSAGE INTERNE
                        $this->creerNotification(
                            null,
                            'annulation_voyage',
                            "Annulation: Votre voyage {$voyage->trajetComplet} a été annulé. Remboursement en cours.",
                            $ticket->idUtilisateur,
                            $ticket->id
                        );

                        if ($resultPush['success']) {
                            $this->info("✅ PUSH + INTERNE: Notification annulation envoyée pour le ticket {$ticket->id}");
                        } else {
                            $this->error("❌ PUSH ÉCHEC: Ticket {$ticket->id} - {$resultPush['error']}");
                        }
                    }
                }
            });
    }

    protected function notificationsAutresIncidents(FirebaseService $firebaseService)
    {
        // Autres types d'incidents
        $this->info("🔍 Vérification d'autres incidents...");
    }

    /**
     * Créer une notification interne
     */
    private function creerNotification($idAbonement, $type, $contenu, $idUser = null, $idTicket = null)
    {
        Message::firstOrCreate([
            'idAbonement' => $idAbonement,
            'idUser' => $idUser,
            'idTicket' => $idTicket,
            'type' => $type,
            'contenu' => $contenu,
            'dateEnvoi' => now()->startOfDay()
        ], [
            'estVu' => false
        ]);
    }
}
