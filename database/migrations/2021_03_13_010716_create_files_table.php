<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
			$table->string('name')->unique();
            $table->timestamps();
        });

		// Also add all existing files on disk to the table
		$files = \Storage::disk('uploads')->allFiles();
		foreach ($files as $file) {
			\App\Models\File::create(['name' => $file]);
		}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
