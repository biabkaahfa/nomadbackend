<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
          DB::table('users')->insert([
        [
            'name' => 'Admin Général',
            'email' => 'admin@system.com',
            'password' => Hash::make('password'),
            'idProfil' => 1,
            'statut' => 'actif',
            'telephone' => '00000000',
            'image' => null,
            'idCompagnie' => null,
            'idGarre' => null
        ],
        [
            'name' => 'Admin compagnie',
            'email' => 'admin@faso.com',
            'password' => Hash::make('password'),
            'idProfil' => 2,
            'statut' => 'inactif',
            'telephone' => '00000000',
            'image' => null,
            'idCompagnie' => null,
            'idGarre' => null
        ]
    ]);
    }
}
