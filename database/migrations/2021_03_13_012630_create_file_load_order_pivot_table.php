<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFileLoadOrderPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_load_order', function (Blueprint $table) {
            $table->unsignedBigInteger('file_id')->index();
            $table->foreign('file_id')->references('id')->on('files')->onDelete('cascade');
            $table->unsignedBigInteger('load_order_id')->index();
            $table->foreign('load_order_id')->references('id')->on('load_orders')->onDelete('cascade');
            $table->primary(['file_id', 'load_order_id']);
        });

		// Also create the entries for each existing list.
		$lists = \App\Models\LoadOrder::all();
		$files = \App\Models\File::all();

		foreach ($lists as $list) {
			$listFiles = explode(',', $list->files);

			foreach ($listFiles as $listFile) {
				foreach ($files as $file) {
					if ($listFile == $file->name) {
						echo $list->id, $file->id;
						$list->files()->attach($file->id);
					}
				}
			}
		}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_load_order');
    }
}
