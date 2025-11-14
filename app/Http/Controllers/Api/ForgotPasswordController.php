<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    // Générer et envoyer un token personnalisé
    public function sendResetToken(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        try {
            // Vérifier si l'utilisateur existe
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun utilisateur trouvé avec cet email.'
                ], 404);
            }

            // Générer un token unique
            $token = Str::random(60);

            // Mettre à jour le remember_token de l'utilisateur
            $user->update([
                'remember_token' => $token,
                'email_verified_at' => Carbon::now() // Optionnel: marquer comme vérifié
            ]);

            // Envoyer l'email avec le token
            $this->sendTokenEmail($user, $token);

            return response()->json([
                'success' => true,
                'message' => 'Token de réinitialisation envoyé par email !',
                'token' => $token // À retirer en production
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi du token: ' . $e->getMessage()
            ], 500);
        }
    }

    // Vérifier la validité du token
    public function verifyToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email'
        ]);

        try {
            $user = User::where('email', $request->email)
                        ->where('remember_token', $request->token)
                        ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token invalide ou expiré.'
                ], 400);
            }

            // Vérifier si le token n'est pas trop ancien (optionnel - 1 heure)
            $tokenCreatedAt = $user->updated_at;
            if ($tokenCreatedAt->diffInHours(Carbon::now()) > 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token expiré. Veuillez en demander un nouveau.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Token valide.',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de vérification: ' . $e->getMessage()
            ], 500);
        }
    }

    // Réinitialiser le mot de passe avec le token
    public function resetPasswordWithToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        try {
            // Trouver l'utilisateur avec le token
            $user = User::where('email', $request->email)
                        ->where('remember_token', $request->token)
                        ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token invalide ou expiré.'
                ], 400);
            }

            // Mettre à jour le mot de passe et effacer le token
            $user->update([
                'password' => Hash::make($request->password),
                'remember_token' => null, // Effacer le token après utilisation
            ]);

            // Envoyer un email de confirmation
            $this->sendPasswordChangedEmail($user);

            return response()->json([
                'success' => true,
                'message' => 'Mot de passe réinitialisé avec succès !'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la réinitialisation: ' . $e->getMessage()
            ], 500);
        }
    }

    // Envoyer l'email avec le token
    private function sendTokenEmail($user, $token)
    {
        $resetUrl = "http://192.168.1.66:8000/reset-password?token=$token&email=" . urlencode($user->email);

        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'token' => $token,
            'resetUrl' => $resetUrl,
            'expiresIn' => '1 heure'
        ];

        // Utiliser votre système d'email
        Mail::send('emails.password_reset', $data, function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Réinitialisation de votre mot de passe - Movyx');
        });
    }

    // Envoyer l'email de confirmation de changement
    private function sendPasswordChangedEmail($user)
    {
        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'changeDate' => Carbon::now()->format('d/m/Y à H:i')
        ];

        Mail::send('emails.password_changed', $data, function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Votre mot de passe a été modifié - Movyx');
        });
    }

    // Nettoyer les tokens expirés (cron job)
    public function cleanupExpiredTokens()
    {
        try {
            $expiredUsers = User::whereNotNull('remember_token')
                               ->where('updated_at', '<', Carbon::now()->subHours(2))
                               ->get();

            foreach ($expiredUsers as $user) {
                $user->update(['remember_token' => null]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Tokens expirés nettoyés: ' . $expiredUsers->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur nettoyage: ' . $e->getMessage()
            ], 500);
        }
    }
}
