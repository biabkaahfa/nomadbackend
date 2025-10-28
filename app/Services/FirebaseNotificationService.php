<?php

namespace App\Services;

use App\Models\User;
use App\Models\Compagnie;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Exception\MessagingException;

class FirebaseNotificationService
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(config('firebase.projects.default.credentials.file'));
        $this->messaging = $factory->createMessaging();
    }

    /**
     * Envoyer une notification à un utilisateur
     */
    public function sendToUser(User $user, string $title, string $body, array $data = [])
    {
        $tokens = $user->fcm_tokens ?? [];

        if (empty($tokens)) {
            return false;
        }

        return $this->sendToTokens($tokens, $title, $body, $data);
    }

    /**
     * Envoyer une notification à une compagnie
     */
    public function sendToCompagnie(Compagnie $compagnie, string $title, string $body, array $data = [])
    {
        $tokens = [];

        // Récupérer tous les tokens FCM des utilisateurs de la compagnie
        foreach ($compagnie->users as $user) {
            $tokens = array_merge($tokens, $user->fcm_tokens ?? []);
        }

        $tokens = array_unique($tokens);

        if (empty($tokens)) {
            return false;
        }

        return $this->sendToTokens($tokens, $title, $body, $data);
    }

    /**
     * Envoyer une notification à des tokens spécifiques
     */
    public function sendToTokens(array $tokens, string $title, string $body, array $data = [])
    {
        if (empty($tokens)) {
            return false;
        }

        $notification = Notification::create($title, $body);

        $message = CloudMessage::new()
            ->withNotification($notification)
            ->withData(array_merge([
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                'sound' => 'default',
            ], $data));

        try {
            if (count($tokens) === 1) {
                // Message single device
                $result = $this->messaging->send($message->withChangedTarget('token', $tokens[0]));
            } else {
                // Message multi devices
                $result = $this->messaging->sendMulticast($message, $tokens);
            }

            \Log::info('Notification envoyée avec succès', [
                'success_count' => $result->successes()->count(),
                'failure_count' => $result->failures()->count(),
            ]);

            return $result;

        } catch (MessagingException $e) {
            \Log::error('Erreur envoi notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Envoyer un rappel de notation
     */
    public function sendRappelNotation(User $user, $voyage, $ticketId)
    {
        return $this->sendToUser(
            $user,
            '📝 Notez votre voyage',
            "Comment s'est passé votre voyage {$voyage} ? Donnez-nous votre avis !",
            [
                'type' => 'rappel_notation',
                'ticket_id' => $ticketId,
                'voyage' => $voyage,
                'screen' => 'notation',
            ]
        );
    }

    /**
     * Envoyer un rappel d'abonnement
     */
    public function sendRappelAbonnement(User $user, $joursRestants)
    {
        return $this->sendToUser(
            $user,
            '⚠️ Abonnement expirant',
            "Votre abonnement expire dans {$joursRestants} jour(s). Renouvelez maintenant !",
            [
                'type' => 'rappel_abonnement',
                'jours_restants' => $joursRestants,
                'screen' => 'abonnement',
            ]
        );
    }

    /**
     * Envoyer une notification de voyage
     */
    public function sendRappelVoyage(User $user, $voyage, $heureDepart)
    {
        return $this->sendToUser(
            $user,
            '🚗 Rappel de voyage',
            "Votre voyage {$voyage} départ dans {$heureDepart}",
            [
                'type' => 'rappel_voyage',
                'voyage' => $voyage,
                'screen' => 'voyage_details',
            ]
        );
    }

    /**
     * Valider un token FCM
     */
    public function validateToken(string $token): bool
    {
        try {
            $this->messaging->validateRegistrationTokens([$token]);
            return true;
        } catch (MessagingException $e) {
            return false;
        }
    }
}
