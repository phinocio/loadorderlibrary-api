<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            GameSeeder::class,
        ]);

        if (! app()->isProduction() && ! app()->environment('testing')) {
            $this->call([
                UserSeeder::class,
                FileSeeder::class,
                LoadOrderSeeder::class,
            ]);
        }
    }
}
