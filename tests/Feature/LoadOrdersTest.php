<?php

namespace Tests\Feature;

use App\Models\LoadOrder;
use Database\Seeders\GamesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoadOrdersTest extends TestCase
{
	use RefreshDatabase, WithFaker;

	protected function setUp(): void
	{
		parent::setUp();
		// $this->seed(GamesTableSeeder::class);
	}

	/** @test */
	public function index_returns_valid_format()
	{
		$this->getJson('/api/lists')->assertJsonStructure(
			[
				"*" => [
					'id',
					'user_id',
					'game_id',
					'slug',
					'name',
					'description',
					'files',
					'is_private',
					'created_at',
					'updated_at'
				]
			]	
		);
	}

	/** @test */
    public function a_guest_can_create_a_list()
    {
		$this->withoutExceptionHandling();
		$attributes = LoadOrder::factory()->raw();

		$this->assertGuest();
        $this->postJson('/api/lists', $attributes)->assertStatus(200)->assertJsonStructure([
			'id',
			'user_id',
			'game_id',
			'slug',
			'name',
			'description',
			'files',
			'is_private',
			'created_at',
			'updated_at'
		]);
		
		$this->assertDatabaseHas('load_orders', $attributes);
    }

	/** @test */
	public function a_list_requires_a_name()
	{
		// $this->withoutExceptionHandling();
		$attributes = LoadOrder::factory()->raw(['name' => '']);
		$this->postJson('/api/lists', $attributes)->assertStatus(422)->assertJsonValidationErrors('name');
	}

	/** @test */
	public function a_list_requires_a_game_id()
	{
		// $this->withoutExceptionHandling();
		$attributes = LoadOrder::factory()->raw(['game_id' => '']);
		$this->postJson('/api/lists', $attributes)->assertStatus(422)->assertJsonValidationErrors('game_id');
	}

}
