<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Google\Client as GoogleClient;
use Google\Service\Oauth2;

class FirebaseServiceV1
{
    protected $accessToken;

    public function __construct()
    {
        $this->accessToken = $this->generateAccessToken();
    }

    /**
     * Génère un token d'accès pour FCM v1
     */
    private function generateAccessToken()
    {
        try {
            $credentialsPath = env('FIREBASE_CREDENTIALS');

            if (!file_exists($credentialsPath)) {
                throw new \Exception("Fichier de credentials Firebase introuvable: " . $credentialsPath);
            }

            $client = new GoogleClient();
            $client->setAuthConfig($credentialsPath);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

            $accessToken = $client->fetchAccessTokenWithAssertion();

            return $accessToken['access_token'] ?? null;

        } catch (\Exception $e) {
            Log::error('Erreur génération token FCM: ' . $e->getMessage());
            return null;
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

        return $results;
    }

    public function sendToToken(string $token, string $title, string $body, array $data = [])
    {
        if (empty($this->accessToken)) {
            Log::error('Token d\'accès FCM non disponible');
            return ['success' => false, 'error' => 'Token d\'accès non disponible'];
        }

        $projectId = env('FIREBASE_PROJECT_ID', 'movyx');
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $payload = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $data,
                'android' => [
                    'notification' => [
                        'sound' => 'default',
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    ]
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                        ]
                    ]
                ]
            ]
        ];

        Log::info('Envoi FCM v1', [
            'token' => substr($token, 0, 20) . '...',
            'title' => $title,
            'has_access_token' => !empty($this->accessToken)
        ]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($url, $payload);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('FCM v1 - Succès', ['result' => $result]);

                return [
                    'success' => true,
                    'response' => $result
                ];
            } else {
                Log::error('FCM v1 - Erreur', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return [
                    'success' => false,
                    'error' => 'Erreur: ' . $response->status(),
                    'body' => $response->body()
                ];
            }

        } catch (\Exception $e) {
            Log::error('Exception FCM v1: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
