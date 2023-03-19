<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSizeInBytesColumnToFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('files', function (Blueprint $table) {
			$table->after('clean_name', function ($table) {
				$table->unsignedBigInteger('size_in_bytes');
			});
        });

		$files = \App\Models\File::all();

		foreach ($files as $file) {
			$file->size_in_bytes = \Storage::disk('uploads')->size($file->name);
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
            $table->dropColumn('size_in_bytes');
        });
    }
}
