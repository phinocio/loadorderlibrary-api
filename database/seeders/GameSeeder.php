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
            'slug' => 'tes3-morrowind',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('games')->insert([
            'id' => 2,
            'name' => 'TESIV Oblivion',
            'slug' => 'tes4-oblivion',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('games')->insert([
            'id' => 3,
            'name' => 'TESV Skyrim LE',
            'slug' => 'tes5-skyrim-le',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('games')->insert([
            'id' => 4,
            'name' => 'TESV Skyrim SE',
            'slug' => 'tes5-skyrim-se',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('games')->insert([
            'id' => 5,
            'name' => 'TESV Skyrim VR',
            'slug' => 'tes5-skyrim-vr',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('games')->insert([
            'id' => 6,
            'name' => 'Fallout 3',
            'slug' => 'fallout-3',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('games')->insert([
            'id' => 7,
            'name' => 'Fallout New Vegas',
            'slug' => 'fallout-new-vegas',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('games')->insert([
            'id' => 8,
            'name' => 'Fallout 4',
            'slug' => 'fallout4',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('games')->insert([
            'id' => 9,
            'name' => 'Fallout 4 VR',
            'slug' => 'fallout4-vr',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('games')->insert([
            'id' => 10,
            'name' => 'Tale of Two Wastelands',
            'slug' => 'tale-of-two-wastelands',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('games')->insert([
            'id' => 11,
            'name' => 'Cyberpunk 2077',
            'slug' => 'cyberpunk-2077',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('games')->insert([
            'id' => 12,
            'name' => 'Darkest Dungeon',
            'slug' => 'darkest-dungeon',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('games')->insert([
            'id' => 13,
            'name' => 'Dark Messiah of Might & Magic',
            'slug' => 'dark-messiah-of-might-magic',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('games')->insert([
            'id' => 14,
            'name' => 'Dark Souls',
            'slug' => 'dark-souls',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('games')->insert([
            'id' => 15,
            'name' => 'Dragon Age II',
            'slug' => 'dragon-age-ii',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('games')->insert([
            'id' => 16,
            'name' => 'Dragon Age: Origins',
            'slug' => 'dragon-age-origins',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('games')->insert([
            'id' => 17,
            'name' => 'Dungeon Siege II',
            'slug' => 'dungeon-siege-ii',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('games')->insert([
            'id' => 18,
            'name' => 'Kerbal Space Program',
            'slug' => 'kerbal-space-program',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('games')->insert([
            'id' => 19,
            'name' => 'Kingdom Come: Deliverance',
            'slug' => 'kingdom-come-deliverance',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('games')->insert([
            'id' => 20,
            'name' => 'Mirror\'s Edge',
            'slug' => 'mirrors-edge',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('games')->insert([
            'id' => 21,
            'name' => 'Mount & Blade II: Bannerlord',
            'slug' => 'mount-blade-ii-bannerlord',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('games')->insert([
            'id' => 22,
            'name' => 'No Man\'s Sky',
            'slug' => 'no-mans-sky',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('games')->insert([
            'id' => 23,
            'name' => 'STALKER Anomaly',
            'slug' => 'stalker-anomaly',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('games')->insert([
            'id' => 24,
            'name' => 'Stardew Valley',
            'slug' => 'stardew-valley',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('games')->insert([
            'id' => 25,
            'name' => 'The Binding of Isaac: Rebirth',
            'slug' => 'the-binding-of-isaac-rebirth',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('games')->insert([
            'id' => 26,
            'name' => 'The Witcher 3: Wild Hunt',
            'slug' => 'the-witcher-3-wild-hunt',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('games')->insert([
            'id' => 27,
            'name' => 'Zeus and Poseidon',
            'slug' => 'zeus-and-poseidon',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('games')->insert([
            'id' => 28,
            'name' => 'Enderal',
            'slug' => 'enderal',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('games')->insert([
            'id' => 29,
            'name' => 'Enderal SE',
            'slug' => 'enderal-se',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('games')->insert([
            'id' => 30,
            'name' => 'Starfield',
            'slug' => 'starfield',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('games')->insert([
            'id' => 31,
            'name' => 'The Witcher: Enhanced Edition',
            'slug' => 'the-witcher-enhanced-edition',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('games')->insert([
            'id' => 32,
            'name' => 'Baldur\'s Gate 3',
            'slug' => 'baldurs-gate-3',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
