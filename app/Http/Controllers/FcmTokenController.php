<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FcmTokenController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Enregistrer un token FCM
     */
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string|min:100',
        ]);

        $user = Auth::user();
        $token = $request->input('token');

        // Ajouter le token à l'utilisateur
        $tokens = $user->fcm_tokens ?? [];

        if (!in_array($token, $tokens)) {
            $tokens[] = $token;
            $user->update(['fcm_tokens' => $tokens]);

            Log::info('Token FCM enregistré', [
                'user_id' => $user->id,
                'token_prefix' => substr($token, 0, 20) . '...',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Token FCM enregistré avec succès'
        ]);
    }

    /**
     * Tester une notification
     */
    public function test(Request $request)
    {
        $user = Auth::user();

        $result = $this->firebaseService->sendToUser(
            $user,
            '🔔 Test Notification',
            'Ceci est une notification de test depuis Movyx!',
            [
                'type' => 'test',
                'screen' => 'home',
                'timestamp' => now()->toISOString(),
            ]
        );

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Notification de test envoyée avec succès',
                'data' => $result,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Échec envoi notification de test',
            'error' => $result['error'],
        ], 500);
    }
}
