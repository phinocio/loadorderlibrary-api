<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('games')->insert([
            'id' => 1,
            'name' => 'TESIII Morrowind',
        ]);

        DB::table('games')->insert([
            'id' => 2,
            'name' => 'TESIV Oblivion',
        ]);

        DB::table('games')->insert([
            'id' => 3,
            'name' => 'TESV Skyrim LE',
        ]);

        DB::table('games')->insert([
            'id' => 4,
            'name' => 'TESV Skyrim SE',
        ]);

        DB::table('games')->insert([
            'id' => 5,
            'name' => 'TESV Skyrim VR',
        ]);

        DB::table('games')->insert([
            'id' => 6,
            'name' => 'Fallout 3',
        ]);

        DB::table('games')->insert([
            'id' => 7,
            'name' => 'Fallout New Vegas',
        ]);

        DB::table('games')->insert([
            'id' => 8,
            'name' => 'Fallout 4',
        ]);

        DB::table('games')->insert([
            'id' => 9,
            'name' => 'Fallout 4 VR',
        ]);

        DB::table('games')->insert([
            'id' => 10,
            'name' => 'Tale of Two Wastelands',
        ]);
    }
}
