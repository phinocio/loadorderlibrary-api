<?php

namespace Database\Seeders;

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
            'name' => '9b76f507722d4a7d2bed09d2e42c3b6c-modlist.txt',
            'clean_name' => 'modlist.txt',
            'size_in_bytes' => 1336,
        ]);

        DB::table('files')->insert([
            'id' => 2,
            'name' => '469e978c1e1fdb071c1a9463d3490fb4-starfieldprefs.ini',
            'clean_name' => 'starfieldprefs.ini',
            'size_in_bytes' => 1936,
        ]);

        DB::table('files')->insert([
            'id' => 3,
            'name' => 'c144f62b2cce9e439f769800a0875057-starfieldcustom.ini',
            'clean_name' => 'starfieldcustom.ini',
            'size_in_bytes' => 2031,
        ]);

        DB::table('files')->insert([
            'id' => 4,
            'name' => '34ba75b3cc3f754a945b71f7e77e72a9-plugins.txt',
            'clean_name' => 'plugins.txt',
            'size_in_bytes' => 9496,
        ]);
        DB::table('files')->insert([
            'id' => 5,
            'name' => '682aeca2cdfc38d288a8b5ede0f33404-starfield.ini',
            'clean_name' => 'starfield.ini',
            'size_in_bytes' => 34,
        ]);

        DB::table('files')->insert([
            'id' => 6,
            'name' => 'f068cf28ab69b86adef434e3c2e15c91-loadorder.txt',
            'clean_name' => 'loadorder.txt',
            'size_in_bytes' => 9203,
        ]);

        DB::table('files')->insert([
            'id' => 7,
            'name' => 'a7e2ec953166e7661b0c00d308a73c9d-modlist.txt',
            'clean_name' => 'modlist.txt',
            'size_in_bytes' => 19253,
        ]);

        DB::table('files')->insert([
            'id' => 8,
            'name' => '99b9f8735fbea0202267dc71047c1c31-plugins.txt',
            'clean_name' => 'plugins.txt',
            'size_in_bytes' => 9480,
        ]);

        DB::table('files')->insert([

            'id' => 9,
            'name' => 'e32248a5e1cbbeba58bee77b7722e316-skyrim.ini',
            'clean_name' => 'skyrim.ini',
            'size_in_bytes' => 3330,
        ]);

        DB::table('files')->insert([

            'id' => 10,
            'name' => '61ba84e4f034734ed60fb7b62d40b643-skyrimcustom.ini',
            'clean_name' => 'skyrimcustom.ini',
            'size_in_bytes' => 3330,
        ]);

        DB::table('files')->insert([
            'id' => 11,
            'name' => 'bdccf7b9c8d3b7f2465314afe4eaa587-skyrimprefs.ini',
            'clean_name' => 'skyrimprefs.ini',
            'size_in_bytes' => 3833,
        ]);
    }
}
