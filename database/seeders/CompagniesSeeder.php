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
            'type'=>'PRIVE',
            'description' => 'Transport et sous regional national',
            'telephone' => '70112233',
            'email' => 'contact@transfaso.com'
        ],
    [
            'name' => 'SOTRACO',
            'logo' => 'transfaso.png',
            'type'=>'PUBLIC',
            'description' => 'Transport urbains',
            'telephone' => '50112233',
            'email' => 'contact@sotraco.com'
        ]]);
    }
}
