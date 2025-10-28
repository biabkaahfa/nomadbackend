<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\GenererVoyages::class,
        \App\Console\Commands\RappelAffectationBus::class,
        \App\Console\Commands\GenererQrCodesTickets::class, // Ajoutez cette ligne
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('voyages:generer')->dailyAt('01:00');
        $schedule->command('voyages:rappel-bus')->dailyAt('06:00');
       // $schedule->command('abonnements:gerer-expiration')->daily();
        $schedule->command('rappels:envoyer')->dailyAt('08:00');
        $schedule->command('qr:clean-expired')->hourly();
        $schedule->command('notifications:envoyer')->everyMinute();

        // Gestion des expirations d'abonnements une fois par jour
        $schedule->command('abonnements:gerer-expiration')->dailyAt('00:00');

        // Nettoyage des tokens FCM invalides une fois par semaine
        $schedule->command('fcm:clean-tokens')->weekly();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
