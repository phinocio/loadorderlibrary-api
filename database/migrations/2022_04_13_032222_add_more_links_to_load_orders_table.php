<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('load_orders', function (Blueprint $table) {
			$table->string('readme')
			->after('website')
			->nullable()
			->default(null);

			$table->string('discord')
			->after('readme')
			->nullable()
			->default(null);
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('load_orders', function (Blueprint $table) {
			$table->dropColumn('readme');
			$table->dropColumn('discord');
        });
    }
};
