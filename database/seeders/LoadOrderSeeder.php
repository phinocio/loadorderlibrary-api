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
        LoadOrder::factory(1000)->create();
        $lists = LoadOrder::all();

        foreach ($lists as $list) {
            $num = rand(1, 5);
            $files = File::get()->random($num)->unique('clean_name');
            $list->files()->attach($files->pluck('id')->toArray());
        }
    }
}
