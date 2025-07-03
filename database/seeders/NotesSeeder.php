<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
                'note' => 1,
                'commentaire' => 'Voyage dérangeant et paisible',
                'dateNote' => Carbon::now(),
            ],
            [
                'idTicket' => 2,
                'note' => 2,
                'commentaire' => 'Voyage calme et paisible',
                'dateNote' => Carbon::now(),
            ],
            // [
            //     'idTicket' => 5,
            //     'note' => 1,
            //     'commentaire' => 'Voyage trouble et paisible',
            //     'dateNote' => Carbon::now(),
            // ],
            // [
            //     'idTicket' => 4,
            //     'note' => 2,
            //     'commentaire' => 'Voyage retardé et paisible',
            //     'dateNote' => Carbon::now(),
            // ],
            // [
            //     'idTicket' => 20,
            //     'note' => 2,
            //     'commentaire' => 'Voyage trop long et paisible',
            //     'dateNote' => Carbon::now(),
            // ],
            // [
            //     'idTicket' => 21,
            //     'note' => 1,
            //     'commentaire' => 'Voyage trop rapide et paisible',
            //     'dateNote' => Carbon::now(),
            // ],
        ]);
    }

    }

