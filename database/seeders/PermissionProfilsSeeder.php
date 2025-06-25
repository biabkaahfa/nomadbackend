<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionProfilsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('permission_profils')->insert([
            // Admin général : toutes permissions
            ['idPermission' => 1, 'idProfil' => 1],
            ['idPermission' => 2, 'idProfil' => 1],
            ['idPermission' => 3, 'idProfil' => 1],

            // Réceptionniste : gestion des tickets seulement
            ['idPermission' => 3, 'idProfil' => 4],
        ]);
    }
}
