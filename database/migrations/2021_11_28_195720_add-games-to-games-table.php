<?php

use Illuminate\Database\Migrations\Migration;

class AddGamesToGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No rollback as foreign keys would be violated the second a list is made
    }
}
