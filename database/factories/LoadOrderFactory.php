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

        return [
            'name' => $this->faker->name(),
            'slug' => $this->faker->slug(),
            'description' => $this->faker->paragraph(),
            'is_private' => $this->faker->boolean(),
			'discord' => rand(1,3) == 1 ? str_replace(['https://', 'http://'], '', 'example.com/discord' ?? null) : null,
			'website' => rand(1,3) == 1 ? str_replace(['https://', 'http://'], '', 'example.com/website' ?? null) : null,
			'readme' => rand(1,3) == 1 ? str_replace(['https://', 'http://'], '', 'example.com/readme' ?? null) : null,
            'game_id' => Game::factory()->create()->id,
            'user_id' => null,
        ];
    }
}
