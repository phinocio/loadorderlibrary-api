<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

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

            //            $table->unsignedBigInteger('file_id')->index();
            //            $table->foreign('file_id')->references('id')->on('files')->onDelete('cascade');
            //            $table->unsignedBigInteger('load_order_id')->index();
            //            $table->foreign('load_order_id')->references('id')->on('load_orders')->onDelete('cascade');
            //            $table->primary(['file_id', 'load_order_id']);
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
