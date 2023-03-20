<?php

use Illuminate\Database\Migrations\Migration;

return new class() extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('games')->insert([
            'id' => 28,
            'name' => 'Enderal',
        ]);

        DB::table('games')->insert([
            'id' => 29,
            'name' => 'Enderal SE',
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
};
