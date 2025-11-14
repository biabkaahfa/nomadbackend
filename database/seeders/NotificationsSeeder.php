<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\Models\Notifications;
use App\Models\Voyages;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class NotificationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer quelques voyages avec des tickets
        $voyages = Voyages::with(['tickets.user'])->take(3)->get();

        // Types de notifications avec leurs configurations
        $notificationTypes = [
            [
                'titre' => 'Retard important',
                'contenu' => 'Votre voyage connaît un retard d\'environ 45 minutes. Nous nous excusons pour la gêne occasionnée.',
                'type' => 'retard',
                'modes_envoi' => ['email', 'sms', 'push'],
                'email_count' => 2,
                'sms_count' => 1,
                'push_count' => 1
            ],
            [
                'titre' => 'Annulation de voyage',
                'contenu' => 'En raison de conditions météorologiques défavorables, votre voyage est annulé. Vous serez remboursé intégralement.',
                'type' => 'annulation',
                'modes_envoi' => ['email', 'sms'],
                'email_count' => 3,
                'sms_count' => 2,
                'push_count' => 0
            ],
            [
                'titre' => 'Changement d\'horaire',
                'contenu' => 'Votre voyage a été reporté de 30 minutes. Le nouveau départ est prévu à 14h30.',
                'type' => 'report',
                'modes_envoi' => ['email', 'push'],
                'email_count' => 2,
                'sms_count' => 0,
                'push_count' => 2
            ],
            [
                'titre' => 'Accident sur la route',
                'contenu' => 'Un accident a été signalé sur votre itinéraire. Le trafic est détourné, prévoyez un retard supplémentaire.',
                'type' => 'accident',
                'modes_envoi' => ['sms', 'push'],
                'email_count' => 0,
                'sms_count' => 3,
                'push_count' => 2
            ],
            [
                'titre' => 'Rappel de voyage',
                'contenu' => 'Rappel : Votre voyage démarre demain à 08h00. Présentez-vous 30 minutes avant le départ.',
                'type' => 'rappel',
                'modes_envoi' => ['email'],
                'email_count' => 4,
                'sms_count' => 0,
                'push_count' => 0
            ]
        ];

        $adminUser = User::whereHas('profil', function($query) {
            $query->where('name', 'Admin général')->orWhere('name', 'Admin compagnie');
        })->first();

        foreach ($voyages as $index => $voyage) {
            // Sélectionner un type de notification (en boucle si plus de voyages que de types)
            $notificationConfig = $notificationTypes[$index % count($notificationTypes)];

            // Calculer les statistiques basées sur les tickets réels
            $ticketsCount = $voyage->tickets->count();
            $emailsCount = $voyage->tickets->filter(function($ticket) {
                return $ticket->email || $ticket->user?->email || $ticket->emailPersonneAPrevenir;
            })->count();

            $smsCount = $voyage->tickets->filter(function($ticket) {
                return $ticket->telephone || $ticket->user?->phone_number || $ticket->telephonePersonneAPrevenir;
            })->count();

            $pushCount = $voyage->tickets->filter(function($ticket) {
                return $ticket->user && $ticket->user->fcm_tokens && count($ticket->user->fcm_tokens) > 0;
            })->count();

            // Ajuster les statistiques selon les modes d'envoi
            $emailSent = in_array('email', $notificationConfig['modes_envoi']);
            $smsSent = in_array('sms', $notificationConfig['modes_envoi']);
            $pushSent = in_array('push', $notificationConfig['modes_envoi']);

            $emailCount = $emailSent ? min($notificationConfig['email_count'], $emailsCount) : 0;
            $smsCount = $smsSent ? min($notificationConfig['sms_count'], $smsCount) : 0;
            $pushCount = $pushSent ? min($notificationConfig['push_count'], $pushCount) : 0;

            // Date d'envoi aléatoire dans les 7 derniers jours
            $dateEnvoie = Carbon::now()->subDays(rand(0, 7))->subHours(rand(0, 23))->subMinutes(rand(0, 59));

            Notifications::create([
                'titre' => $notificationConfig['titre'],
                'contenu' => $notificationConfig['contenu'],
                'type' => $notificationConfig['type'],
                'DateEnvoie' => $dateEnvoie,
                'idVoyage' => $voyage->id,
                'idUtilisateur' => $adminUser->id ?? 1,

                // Nouveaux champs
                'email_sent' => $emailSent,
                'sms_sent' => $smsSent,
                'push_sent' => $pushSent,
                'email_count' => $emailCount,
                'sms_count' => $smsCount,
                'push_count' => $pushCount,

                'isRead' => rand(0, 1), // Certaines notifications marquées comme lues
                'created_at' => $dateEnvoie,
                'updated_at' => $dateEnvoie,
            ]);

            // Log pour le débogage
            $this->command->info("Notification créée : {$notificationConfig['titre']} pour le voyage {$voyage->id}");
            $this->command->info("Statistiques - Email: {$emailCount}, SMS: {$smsCount}, Push: {$pushCount}");
        }

        // Créer quelques notifications supplémentaires pour avoir plus de données
        $this->createAdditionalNotifications($voyages, $adminUser);
    }

    /**
     * Créer des notifications supplémentaires pour enrichir les données
     */
    private function createAdditionalNotifications($voyages, $adminUser): void
    {
        $additionalNotifications = [
            [
                'titre' => 'Maintenance du bus',
                'contenu' => 'Votre bus est en maintenance. Un véhicule de remplacement a été affecté à votre voyage.',
                'type' => 'retard',
                'modes_envoi' => ['email', 'sms'],
                'voyage_index' => 0
            ],
            [
                'titre' => 'Changement de gare',
                'contenu' => 'Le départ se fera depuis la gare principale au lieu de la gare secondaire.',
                'type' => 'report',
                'modes_envoi' => ['push'],
                'voyage_index' => 1
            ],
            [
                'titre' => 'Offre spéciale',
                'contenu' => 'Profitez de 20% de réduction sur votre prochain voyage avec le code PROMO20.',
                'type' => 'rappel',
                'modes_envoi' => ['email'],
                'voyage_index' => 2
            ]
        ];

        foreach ($additionalNotifications as $config) {
            if (isset($voyages[$config['voyage_index']])) {
                $voyage = $voyages[$config['voyage_index']];

                $ticketsCount = $voyage->tickets->count();
                $emailCount = in_array('email', $config['modes_envoi']) ? rand(1, $ticketsCount) : 0;
                $smsCount = in_array('sms', $config['modes_envoi']) ? rand(1, $ticketsCount) : 0;
                $pushCount = in_array('push', $config['modes_envoi']) ? rand(1, $ticketsCount) : 0;

                $dateEnvoie = Carbon::now()->subDays(rand(8, 14))->subHours(rand(0, 23));

                Notifications::create([
                    'titre' => $config['titre'],
                    'contenu' => $config['contenu'],
                    'type' => $config['type'],
                    'DateEnvoie' => $dateEnvoie,
                    'idVoyage' => $voyage->id,
                    'idUtilisateur' => $adminUser->id ?? 1,

                    'email_sent' => in_array('email', $config['modes_envoi']),
                    'sms_sent' => in_array('sms', $config['modes_envoi']),
                    'push_sent' => in_array('push', $config['modes_envoi']),
                    'email_count' => $emailCount,
                    'sms_count' => $smsCount,
                    'push_count' => $pushCount,

                    'isRead' => true, // Ces anciennes notifications sont marquées comme lues
                    'created_at' => $dateEnvoie,
                    'updated_at' => $dateEnvoie,
                ]);
            }
        }
    }
}
