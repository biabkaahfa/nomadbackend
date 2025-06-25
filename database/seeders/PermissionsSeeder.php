<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('permissions')->insert([
        ['name' => 'voir_users'],
        ['name' => 'créer_trajet'],
        ['name' => 'gérer_tickets'],
        // Ajoute les permissions que tu veux
    ]);
    }
}
