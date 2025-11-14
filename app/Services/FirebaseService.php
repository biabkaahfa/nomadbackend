<?php


namespace App\Services;

use App\Models\User;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        try {
            $credentialsPath = storage_path('firebase-credentials.json');

            if (!file_exists($credentialsPath)) {
                throw new \Exception("Fichier Firebase credentials introuvable: " . $credentialsPath);
            }

            $this->messaging = (new Factory)
                ->withServiceAccount($credentialsPath)
                ->createMessaging();

            Log::info('Firebase Messaging initialisé avec succès');

        } catch (\Exception $e) {
            Log::error('Erreur initialisation Firebase: ' . $e->getMessage());
            throw $e;
        }
    }

    public function sendToUser(User $user, string $title, string $body, array $data = [])
    {
        $tokens = $user->fcm_tokens ?? [];

        if (empty($tokens)) {
            Log::warning('Aucun token FCM pour l\'utilisateur', ['user_id' => $user->id]);
            return ['success' => false, 'error' => 'Aucun token FCM disponible'];
        }

        $results = [];
        foreach ($tokens as $token) {
            $results[$token] = $this->sendToToken($token, $title, $body, $data);
        }

        $successCount = count(array_filter($results, function($r) { return $r['success']; }));

        return [
            'success' => $successCount > 0,
            'success_count' => $successCount,
            'failure_count' => count($tokens) - $successCount,
            'results' => $results
        ];
    }

    public function sendToToken(string $token, string $title, string $body, array $data = [])
    {
        try {
            Log::info('Envoi notification FCM', [
                'token' => substr($token, 0, 20) . '...',
                'title' => $title,
                'body' => $body
            ]);

            // Créer la notification
            $notification = Notification::create($title, $body);

            // Créer le message avec données personnalisées
            $message = CloudMessage::withTarget('token', $token)
                ->withNotification($notification)
                ->withData(array_merge([
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                ], $data));

            // Envoyer le message
            $response = $this->messaging->send($message);

            Log::info('✅ Notification FCM envoyée avec succès', [
                'token' => substr($token, 0, 20) . '...',
                'message_id' => $response
            ]);

            return [
                'success' => true,
                'message_id' => $response,
                'response' => $response
            ];

        } catch (\Exception $e) {
            Log::error('❌ Erreur envoi FCM: ' . $e->getMessage(), [
                'token' => substr($token, 0, 20) . '...',
                'error_details' => $e->getMessage()
            ]);

            // Si le token est invalide, le supprimer
            if (str_contains($e->getMessage(), 'invalid') ||
                str_contains($e->getMessage(), 'not registered') ||
                str_contains($e->getMessage(), 'registration-token')) {

                // Vous pouvez appeler une méthode pour nettoyer le token si nécessaire
                Log::warning('Token FCM invalide détecté', ['token' => substr($token, 0, 20) . '...']);
            }

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Envoyer à multiple devices
     */
    public function sendToTokens(array $tokens, string $title, string $body, array $data = [])
    {
        if (empty($tokens)) {
            return ['success' => false, 'error' => 'Aucun token fourni'];
        }

        $results = [];
        foreach ($tokens as $token) {
            $results[$token] = $this->sendToToken($token, $title, $body, $data);
        }

        return $results;
    }
}
