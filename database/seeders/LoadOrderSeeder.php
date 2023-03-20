<?php

namespace Database\Seeders;

use App\Models\LoadOrder;
use Illuminate\Database\Seeder;

class LoadOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LoadOrder::factory(5)->create();
    }
}
