<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCleanNameColumnToFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('files', function (Blueprint $table) {
			$table->after('name', function ($table) {
				$table->string('clean_name');
			});
        });

		$files = \App\Models\File::all();

		foreach ($files as $file) {
			$cleanName = explode('-', $file->name);
			$file->clean_name = $cleanName[1];
			$file->save();
		}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('files', function (Blueprint $table) {
            $table->dropColumn('clean_name');
        });
    }
}
