<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('file_load_order', function (Blueprint $table) {
            $table->foreignId('file_id')
                ->index()
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('load_order_id')
                ->index()
                ->constrained()
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_load_order');
    }
};
