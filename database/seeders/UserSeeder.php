<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class UserSeeder extends Seeder
{
    /** Run the database seeds. */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Phinocio',
            'email' => 'contact@phinocio.com',
            'password' => Hash::make('supersecret'),
            'is_admin' => true,
            'is_verified' => true,
        ]);

        User::factory(30)->create();
    }
}
