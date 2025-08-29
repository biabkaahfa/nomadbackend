<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
         $this->call([
        ProfilsSeeder::class,
        PermissionsSeeder::class,
        TypeAbonementSeeder::class,
        CompagniesSeeder::class,
        ParametresSeeder::class,
        AbonementSeeder::class,
        MessagesSeeder::class,
        UsersSeeder::class,
        GarresSeeder::class,
        TrajetsSeeder::class,
        BusSeeder::class,
        VoyagesSeeder::class,
        PaiementsSeeder::class,
        TicketsSeeder::class,
         FrequenceTrajetsSeeder::class,
        PermissionProfilsSeeder::class,
         NotesSeeder::class,
           NotificationsSeeder::class,
        GarreTrajetsSeeder::class,
    ]);
    }
}
