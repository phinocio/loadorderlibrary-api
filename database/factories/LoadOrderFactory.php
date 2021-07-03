<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\LoadOrder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

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
			'slug' => $this->faker->slug(),
			'description' => $this->faker->paragraph(),
			'is_private' => $this->faker->boolean(),
			'game_id' => Game::factory()->create()->id,
			'user_id' => null,
			'files' => [
				new UploadedFile(base_path('test-lists/RL Skyrim/modlist.txt'), 'modlist.txt'),
				new UploadedFile(base_path('test-lists/RL Skyrim/plugins.txt'), 'plugins.txt')
			]
        ];
    }
}
