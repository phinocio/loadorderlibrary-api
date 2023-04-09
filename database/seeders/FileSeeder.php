<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
		DB::table('files')->insert([
			'id' => 1,
			'name' => '99b9f8735fbea0202267dc71047c1c31-plugins.txt',
			'clean_name' => 'plugins.txt',
			'size_in_bytes' => 9480,
			'created_at' => Carbon::now(),
			'updated_at' => Carbon::now()
		]);
    }
}
