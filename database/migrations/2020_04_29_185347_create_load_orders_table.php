<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoadOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('load_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreignId('game_id')
                ->references('id')
                ->on('games');
            $table->string('slug', 100)->unique();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->string('files');
            $table->boolean('is_private')->default(false);
            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('load_orders');
    }
}
