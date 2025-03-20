<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('load_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('game_id')->constrained('games');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('version')->nullable()->default(null);
            $table->text('description')->nullable()->default(null);
            $table->string('verison')->nullable()->default(null);
            $table->string('discord')->nullable()->default(null);
            $table->string('readme')->nullable()->default(null);
            $table->string('website')->nullable()->default(null);
            $table->boolean('is_private')->default(false);
            $table->timestamp('expires_at')->nullable()->default(null);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('load_orders');
    }
};
