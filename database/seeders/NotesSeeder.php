<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class NotesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('notes')->insert([
            [
                'idTicket' => 1,
                'securite' => 4,
                'confort' => 3,
                'ponctualite' => 5,
                'accueil' => 4,
                'proprete' => 3,
                'note_globale' => 4,
                'commentaire' => 'Voyage dérangeant et paisible',
                'dateNote' => Carbon::now(),
            ],
            [
                'idTicket' => 2,
                'securite' => 3,
                'confort' => 4,
                'ponctualite' => 2,
                'accueil' => 5,
                'proprete' => 4,
                'note_globale' => 4,
                'commentaire' => 'Voyage calme et paisible',
                'dateNote' => Carbon::now(),
            ],
            // [
            //     'idTicket' => 5,
            //     'securite' => 5,
            //     'confort' => 2,
            //     'ponctualite' => 4,
            //     'accueil' => 3,
            //     'proprete' => 5,
            //     'note_globale' => 4,
            //     'commentaire' => 'Voyage trouble et paisible',
            //     'dateNote' => Carbon::now(),
            // ],
            // [
            //     'idTicket' => 4,
            //     'securite' => 2,
            //     'confort' => 4,
            //     'ponctualite' => 1,
            //     'accueil' => 4,
            //     'proprete' => 3,
            //     'note_globale' => 3,
            //     'commentaire' => 'Voyage retardé et paisible',
            //     'dateNote' => Carbon::now(),
            // ],
            // [
            //     'idTicket' => 20,
            //     'securite' => 3,
            //     'confort' => 2,
            //     'ponctualite' => 3,
            //     'accueil' => 4,
            //     'proprete' => 2,
            //     'note_globale' => 3,
            //     'commentaire' => 'Voyage trop long et paisible',
            //     'dateNote' => Carbon::now(),
            // ],
            // [
            //     'idTicket' => 21,
            //     'securite' => 4,
            //     'confort' => 5,
            //     'ponctualite' => 5,
            //     'accueil' => 3,
            //     'proprete' => 4,
            //     'note_globale' => 4,
            //     'commentaire' => 'Voyage trop rapide et paisible',
            //     'dateNote' => Carbon::now(),
            // ],
        ]);
    }
}
