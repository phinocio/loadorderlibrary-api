<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\LoadOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoadOrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LoadOrder::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
			'name' => $this->faker->name(),
			'slug' => $this->faker->slug(3),
			'description' => $this->faker->paragraph(),
			'files' => $this->faker->word(),
			'is_private' => $this->faker->boolean(),
			'game_id' => Game::factory()->create()->id,
			'user_id' => null
        ];
    }
}
