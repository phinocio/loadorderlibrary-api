<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('load_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('game_id')
                ->constrained();

            $table->string('slug', 100)->unique();
            $table->string('name', 100);
            $table->text('description')->nullable()->default(null);
            $table->string('version', 15)->nullable()->default(null);
            $table->string('website')->nullable()->default(null);
            $table->string('readme')->nullable()->default(null);
            $table->string('discord')->nullable()->default(null);
            $table->boolean('is_private')->default(false);
            $table->timestamp('expires_at')->nullable()->default(null);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('load_orders');
    }
};
