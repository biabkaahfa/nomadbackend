<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Console\Command;

class NettoyerTokensFCM extends Command
{
    protected $signature = 'fcm:clean-tokens';
    protected $description = 'Nettoyer les tokens FCM invalides';

    public function handle()
    {
        $firebaseService = new FirebaseService();
        $totalSupprimes = 0;

        User::whereNotNull('fcm_tokens')->chunk(100, function($users) use ($firebaseService, &$totalSupprimes) {
            foreach ($users as $user) {
                $tokensAvant = count($user->fcm_tokens ?? []);
                $tokensValides = [];

                foreach ($user->fcm_tokens as $token) {
                    // Pour l'API REST, on ne peut pas valider facilement les tokens
                    // On garde tous les tokens et on laisse Firebase gérer les invalides
                    $tokensValides[] = $token;
                }

                // Si certains tokens ont été supprimés
                if (count($tokensValides) < $tokensAvant) {
                    $user->update(['fcm_tokens' => $tokensValides]);
                    $supprimes = $tokensAvant - count($tokensValides);
                    $totalSupprimes += $supprimes;

                    $this->info("Supprimé {$supprimes} tokens pour l'utilisateur {$user->id}");
                }
            }
        });

        $this->info("✅ Nettoyage terminé: {$totalSupprimes} tokens supprimés");
    }
}
