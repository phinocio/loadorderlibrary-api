<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Game;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
 */
final class GameFactory extends Factory
{
    protected $model = Game::class;

    public function definition(): array
    {
        // Slug is generated automatically based on the name
        return [
            'name' => $this->faker->word(),
        ];
    }
}
