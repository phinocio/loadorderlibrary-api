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
            'name' => $this->faker->sentence(rand(1, 7)),
            'description' => rand(1, 100) <= 5 ? $this->faker->paragraph() : null,
            'is_private' => rand(1, 3) === 1,
            'discord' => rand(1, 3) === 1 ? str_replace(['https://', 'http://'], '', 'http://example.com/discord') : null,
            'website' => rand(1, 3) === 1 ? str_replace(['https://', 'http://'], '', 'http://example.com/website') : null,
            'readme' => rand(1, 3) === 1 ? str_replace(['https://', 'http://'], '', 'https://example.com/readme') : null,
            'game_id' => Game::all('id')->random()->id,
            'user_id' => rand(1, 3) === 1 ? User::all('id')->random()->id : null,
        ];
    }
}
