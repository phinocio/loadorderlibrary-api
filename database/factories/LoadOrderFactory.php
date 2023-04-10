<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LoadOrder>
 */
class LoadOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
		$games = Game::count();
		$users = User::count();

        return [
            'name' => $this->faker->name(),
            'slug' => $this->faker->slug(),
            'description' => $this->faker->paragraph(),
            'is_private' => $this->faker->boolean(),
			'discord' => rand(1,3) == 1 ? str_replace(['https://', 'http://'], '', $this->faker->url() ?? null) : null,
			'website' => rand(1,3) == 1 ? str_replace(['https://', 'http://'], '', $this->faker->url() ?? null) : null,
			'readme' => rand(1,3) == 1 ? str_replace(['https://', 'http://'], '', $this->faker->url() ?? null) : null,
            'game_id' => rand(1, $games),
            'user_id' => rand(1, $users),
        ];
    }
}
