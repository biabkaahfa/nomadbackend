<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VoyagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('voyages')->insert([
            [
                'heuresDepart' => '08:00:00',
                'idTrajet' => 1,
                'dateDepart' => Carbon::now()->addDays(1)->format('Y-m-d'),
                'idBus' => 1
            ]
        ]);
    }
}
