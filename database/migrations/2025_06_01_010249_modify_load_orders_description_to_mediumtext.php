<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Run the migrations. */
    public function up(): void
    {
        Schema::table('load_orders', function (Blueprint $table) {
            $table->mediumText('description')->nullable()->default(null)->change();
        });
    }

    /** Reverse the migrations. */
    public function down(): void
    {
        Schema::table('load_orders', function (Blueprint $table) {
            $table->text('description')->nullable()->default(null)->change();
        });
    }
};
