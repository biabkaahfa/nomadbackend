<?php
// app/Services/AqilasSmsService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AqilasSmsService
{
    private string $baseUrl;
    private string $authToken;

    public function __construct()
    {
        $this->baseUrl = 'https://www.aqilas.com/api/v1';
        $this->authToken = config('services.aqilas.auth_token');
    }

    public function sendSms(string $to, string $message, ?string $sender = null, ?string $sendAt = null): array
    {
        try {
            $payload = [
                'from' => $sender ?? config('services.aqilas.default_sender', 'MOVYX'),
                'text' => $message,
                'to' => [$to],
            ];

            if ($sendAt) {
                $payload['send_at'] = $sendAt;
            }

            $response = Http::withHeaders([
                'X-AUTH-TOKEN' => $this->authToken,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/sms", $payload);

            $result = $response->json();

            Log::info('Réponse Aqilas SMS', [
                'to' => $to,
                'success' => $result['success'] ?? false,
                'message' => $result['message'] ?? 'No message',
                'bulk_id' => $result['bulk_id'] ?? null,
                'cost' => $result['cost'] ?? null
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('Erreur envoi SMS Aqilas', [
                'to' => $to,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function sendBulkSms(array $recipients, string $message, ?string $sender = null, ?string $sendAt = null): array
    {
        try {
            $payload = [
                'from' => $sender ?? config('services.aqilas.default_sender', 'MOTCLE'),
                'text' => $message,
                'to' => $recipients,
            ];

            if ($sendAt) {
                $payload['send_at'] = $sendAt;
            }

            $response = Http::withHeaders([
                'X-AUTH-TOKEN' => $this->authToken,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/sms", $payload);

            $result = $response->json();

            Log::info('Réponse Aqilas SMS groupé', [
                'recipients_count' => count($recipients),
                'success' => $result['success'] ?? false,
                'message' => $result['message'] ?? 'No message',
                'bulk_id' => $result['bulk_id'] ?? null,
                'cost' => $result['cost'] ?? null
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('Erreur envoi SMS groupé Aqilas', [
                'recipients_count' => count($recipients),
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
