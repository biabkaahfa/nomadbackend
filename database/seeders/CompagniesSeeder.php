<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompagniesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('compagnies')->insert([
        [
            'name' => 'TransFaso',
            'logo' => 'transfaso.png',
            'description' => 'Transport national',
            'telephone' => '70112233',
            'email' => 'contact@transfaso.com'
        ]]);
    }
}
