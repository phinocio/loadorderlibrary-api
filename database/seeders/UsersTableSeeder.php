<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('users')->insert([
			'name' => 'Phinocio',
			'email' => 'contact@phinocio.com',
			'password' => \Hash::make('supersecret'),
			'is_admin' => true
		]);
    }
}
