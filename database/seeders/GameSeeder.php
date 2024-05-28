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

        DB::table('games')->insert([
            'id' => 11,
            'name' => 'Cyberpunk 2077',
        ]);

        DB::table('games')->insert([
            'id' => 12,
            'name' => 'Darkest Dungeon',
        ]);

        DB::table('games')->insert([
            'id' => 13,
            'name' => 'Dark Messiah of Might & Magic',
        ]);

        DB::table('games')->insert([
            'id' => 14,
            'name' => 'Dark Souls',
        ]);

        DB::table('games')->insert([
            'id' => 15,
            'name' => 'Dragon Age II',
        ]);

        DB::table('games')->insert([
            'id' => 16,
            'name' => 'Dragon Age: Origins',
        ]);

        DB::table('games')->insert([
            'id' => 17,
            'name' => 'Dungeon Siege II',
        ]);

        DB::table('games')->insert([
            'id' => 18,
            'name' => 'Kerbal Space Program',
        ]);

        DB::table('games')->insert([
            'id' => 19,
            'name' => 'Kingdom Come: Deliverance',
        ]);

        DB::table('games')->insert([
            'id' => 20,
            'name' => 'Mirror\'s Edge',
        ]);

        DB::table('games')->insert([
            'id' => 21,
            'name' => 'Mount & Blade II: Bannerlord',
        ]);

        DB::table('games')->insert([
            'id' => 22,
            'name' => 'No Man\'s Sky',
        ]);

        DB::table('games')->insert([
            'id' => 23,
            'name' => 'STALKER Anomaly',
        ]);

        DB::table('games')->insert([
            'id' => 24,
            'name' => 'Stardew Valley',
        ]);

        DB::table('games')->insert([
            'id' => 25,
            'name' => 'The Binding of Isaac: Rebirth',
        ]);

        DB::table('games')->insert([
            'id' => 26,
            'name' => 'The Witcher 3: Wild Hunt',
        ]);

        DB::table('games')->insert([
            'id' => 27,
            'name' => 'Zeus and Poseidon',
        ]);

        DB::table('games')->insert([
            'id' => 28,
            'name' => 'Enderal',
        ]);

        DB::table('games')->insert([
            'id' => 29,
            'name' => 'Enderal SE',
        ]);

        DB::table('games')->insert([
            'id' => 30,
            'name' => 'Starfield',
        ]);

        DB::table('games')->insert([
            'id' => 31,
            'name' => 'The Witcher: Enhanced Edition',
        ]);
    }
}
