<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserProfile>
 */
final class UserProfileFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'bio' => fake()->optional()->text(),
            'discord' => fake()->optional()->url(),
            'kofi' => fake()->optional()->url(),
            'patreon' => fake()->optional()->url(),
            'website' => fake()->optional()->url(),
        ];
    }
}
