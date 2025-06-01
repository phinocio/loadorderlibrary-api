<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class UserSeeder extends Seeder
{
    /** Run the database seeds. */
    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'name' => 'Phinocio',
            'email' => 'contact@phinocio.com',
            'password' => Hash::make('supersecret'),
            'is_admin' => true,
            'is_verified' => true,
        ]);

        $users = [];
        $now = now();
        $password = Hash::make('password');

        for ($i = 0; $i < 3000; $i++) {
            $users[] = [
                'name' => fake()->unique()->name(),
                'email' => fake()->unique()->safeEmail(),
                'password' => $password,
                'email_verified_at' => null,
                'remember_token' => Str::random(60),
                'is_verified' => fake()->boolean(10),
                'is_admin' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Insert in chunks of 500
        foreach (array_chunk($users, 500) as $chunk) {
            DB::table('users')->insert($chunk);
        }
    }
}
