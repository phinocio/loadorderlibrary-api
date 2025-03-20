<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Phinocio',
            'email' => 'contact@phinocio.com',
            'password' => Hash::make('supersecret'),
            'is_admin' => true,
            'is_verified' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        User::factory(5)->create();
    }
}
