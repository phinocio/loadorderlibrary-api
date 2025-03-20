<?php

namespace Database\Seeders;

use App\Models\File;
use App\Models\LoadOrder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LoadOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lists = LoadOrder::factory(3000)->create();

        foreach ($lists as $list) {
            $num = rand(1, 5);
            $files = File::query()->get()->random($num)->unique('clean_name');
            $list->files()->attach($files->pluck('id')->toArray());
        }
    }
}
