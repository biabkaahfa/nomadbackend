<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    public function sendToUser(User $user, string $title, string $body, array $data = [])
    {
        $tokens = $user->fcm_tokens ?? [];

        if (empty($tokens)) {
            Log::warning('Aucun token FCM pour l\'utilisateur', ['user_id' => $user->id]);
            return ['success' => false, 'error' => 'Aucun token FCM disponible'];
        }

        return $this->sendToTokens($tokens, $title, $body, $data);
    }

    public function sendToTokens(array $tokens, string $title, string $body, array $data = [])
    {
        if (empty($tokens)) {
            return ['success' => false, 'error' => 'Aucun token fourni'];
        }

        $serverKey = env('FIREBASE_SERVER_KEY');

        if (empty($serverKey)) {
            return ['success' => false, 'error' => 'Clé serveur Firebase non configurée'];
        }

        $payload = [
            'registration_ids' => $tokens,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ],
            'data' => array_merge([
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ], $data),
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://fcm.googleapis.com/fcm/send', $payload);

            $result = $response->json();

            if ($response->successful()) {
                Log::info('Notification Firebase envoyée', [
                    'success' => $result['success'] ?? 0,
                    'failure' => $result['failure'] ?? 0,
                    'tokens_count' => count($tokens),
                ]);

                return [
                    'success' => true,
                    'success_count' => $result['success'] ?? 0,
                    'failure_count' => $result['failure'] ?? 0,
                    'results' => $result['results'] ?? [],
                ];
            } else {
                Log::error('Erreur Firebase API', [
                    'status' => $response->status(),
                    'response' => $result,
                ]);

                return [
                    'success' => false,
                    'error' => $result['error'] ?? 'Erreur Firebase: ' . $response->status(),
                    'status' => $response->status(),
                ];
            }

        } catch (\Exception $e) {
            Log::error('Exception Firebase: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
