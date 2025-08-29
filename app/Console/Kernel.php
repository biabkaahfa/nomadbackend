<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\GenererVoyages::class,
        \App\Console\Commands\RappelAffectationBus::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('voyages:generer')->dailyAt('01:00');
        $schedule->command('voyages:rappel-bus')->dailyAt('06:00');
        $schedule->command('rappels:envoyer')->dailyAt('08:00');
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
