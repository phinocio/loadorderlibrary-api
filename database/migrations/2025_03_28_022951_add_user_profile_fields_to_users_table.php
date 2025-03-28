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
        Schema::table('users', function (Blueprint $table) {
            $table->text('bio')->nullable();
            $table->string('discord')->nullable();
            $table->string('kofi')->nullable();
            $table->string('patreon')->nullable();
            $table->string('website')->nullable();
        });
    }

    /** Reverse the migrations. */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('bio');
            $table->dropColumn('discord');
            $table->dropColumn('kofi');
            $table->dropColumn('patreon');
            $table->dropColumn('website');
        });
    }
};
