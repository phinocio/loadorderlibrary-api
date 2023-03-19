<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpiresColumnToLoadOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('load_orders', function (Blueprint $table) {
			$table->timestamp('expires_at')
			->after('is_private')
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
			$table->dropColumn('expires_at');
		});
    }
}
