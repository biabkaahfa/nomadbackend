<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfilsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('profils')->insert([
        ['name' => 'Admin général', 'description' => 'Super admin'],
        ['name' => 'Admin compagnie', 'description' => 'Gère la compagnie'],
        ['name' => 'Chef de gare', 'description' => 'Gère les gares'],
        ['name' => 'Réceptionniste', 'description' => 'Réception et réservation'],
        ['name' => 'Contrôleur', 'description' => 'Validation des tickets'],
        ['name' => 'Client', 'description' => 'Utilisateur standard']
    ]);
    }
}
