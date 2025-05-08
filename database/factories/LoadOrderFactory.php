<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Game;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LoadOrder>
 */
final class LoadOrderFactory extends Factory
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
            'description' => $this->faker->boolean(75) ? $this->faker->paragraph(rand(1, 3)) : null,
            'version' => $this->faker->optional(0.8)->semver(),
            'is_private' => $this->faker->boolean(33),
            'discord' => $this->faker->optional(0.4)->url(),
            'website' => $this->faker->optional(0.4)->url(),
            'readme' => $this->faker->optional(0.6)->url(),
            'expires_at' => $this->faker->optional(0.1)->dateTimeBetween('now', '+1 year'),
            'user_id' => User::factory(),
            'game_id' => Game::factory(),
        ];
    }

    /** Indicate that the load order is public. */
    public function public(): self
    {
        return $this->state(fn (array $attributes) => [
            'is_private' => false,
        ]);
    }

    /** Indicate that the load order is private. */
    public function private(): self
    {
        return $this->state(fn (array $attributes) => [
            'is_private' => true,
        ]);
    }
}
