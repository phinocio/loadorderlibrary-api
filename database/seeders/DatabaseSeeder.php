<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    /** Seed the application's database. */
    public function run(): void
    {
        $this->call([
            GameSeeder::class,
        ]);

        if (! app()->isProduction() && ! app()->environment('testing')) {
            $this->call([
                UserSeeder::class,
                UserProfileSeeder::class,
                // FileSeeder::class,
                // LoadOrderSeeder::class,
            ]);
        }
    }
}
