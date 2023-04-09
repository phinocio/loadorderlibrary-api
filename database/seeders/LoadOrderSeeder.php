<?php

namespace Database\Seeders;

use App\Models\File;
use App\Models\LoadOrder;
use Illuminate\Database\Seeder;

class LoadOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LoadOrder::factory(1500)->create();

		$filesCount = File::count();

		LoadOrder::all()->each(function ($list) use ($filesCount) {
			$list->files()->attach(
				File::all()->random(rand(1, $filesCount))->pluck('id')->toArray()
			);
		});
    }
}
