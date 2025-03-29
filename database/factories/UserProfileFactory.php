<?php

namespace Database\Factories;

use App\Models\UserProfile;
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
            'bio' => $this->faker->text(100),
            'discord' => $this->faker->text(100),
            'kofi' => $this->faker->text(100),
            'patreon' => $this->faker->text(100),
            'website' => $this->faker->text(100),
        ];
    }
}