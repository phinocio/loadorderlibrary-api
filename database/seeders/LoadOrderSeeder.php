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
        $lists = LoadOrder::factory(3000)->create();
        $files = File::all();

        foreach ($lists as $list) {
            $randomCount = rand(1, 5);
            $randomFiles = $files->random($randomCount)->pluck('id')->toArray();
            $list->files()->attach($randomFiles);
        }
    }
}
