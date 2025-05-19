<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\File;
use App\Models\Game;
use App\Models\LoadOrder;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class LoadOrderSeeder extends Seeder
{
    /** Run the database seeds. */
    public function run(): void
    {
        $games = Game::all();
        $users = User::all();

        $lists = [];
        $now = now();

        for ($i = 0; $i < 5000; $i++) {
            $name = fake()->unique()->sentence(rand(1, 7));
            $lists[] = [
                'name' => $name,
                'slug' => str($name)->slug(),
                'description' => fake()->boolean(75) ? fake()->paragraph(rand(1, 3)) : null,
                'version' => fake()->optional(0.1)->semver(),
                'is_private' => fake()->boolean(33),
                'discord' => fake()->optional(0.4)->url(),
                'website' => fake()->optional(0.4)->url(),
                'readme' => fake()->optional(0.6)->url(),
                'expires_at' => fake()->optional(0.1)->dateTimeBetween('now', '+1 year'),
                'user_id' => fake()->boolean(33) ? null : $users->random()->id,
                'game_id' => $games->random()->id,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($lists, 500) as $chunk) {
            DB::table('load_orders')->insert($chunk);
        }

        $files = File::all();
        $lists = LoadOrder::all();

        $pivotData = $lists->flatMap(function ($list) use ($files, $now) {
            $randomCount = rand(1, 3);
            $randomFiles = $files->random($randomCount);

            return $randomFiles->map(function ($file) use ($list, $now) {
                return [
                    'load_order_id' => $list->id,
                    'file_id' => $file->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            });
        })->all();

        foreach (array_chunk($pivotData, 500) as $chunk) {
            DB::table('file_load_order')->insert($chunk);
        }
    }
}
