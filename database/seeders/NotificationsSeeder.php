<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\Models\Notifications;
use App\Models\Voyages;
use Illuminate\Support\Facades\DB;

class NotificationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
         $tickets = Voyages::take(2)->get();

        foreach ($tickets as $ticket) {
            Notifications::create([
                'titre' => 'Changement d\'horaire',
                'contenu' => 'Votre voyage a été reporté à une nouvelle heure. Veuillez consulter l\'application.',
                'type' => 'report',
                'DateEnvoie' => Carbon::now(),
                'idVoyage' => $ticket->id,
            ]);
    }
}
}
