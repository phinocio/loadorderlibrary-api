<?php

namespace Tests\Feature;

use App\Models\LoadOrder;
use Database\Seeders\GamesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Storage;
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
		$files = [
			UploadedFile::fake()->create('modlist.txt', 1),
			UploadedFile::fake()->create('plugins.txt', 3)
		];

		$attributes = LoadOrder::factory()->raw(['files' => $files]);

		$this->assertGuest();
        $response = $this->postJson('/api/lists', $attributes)->assertStatus(200)->assertJsonStructure([
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
		
		$this->assertDatabaseHas('load_orders', $response->json());
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

	/** @test */
	public function a_list_requires_files()
	{
		$attributes = LoadOrder::factory()->raw(['files' => '']);
		$this->postJson('/api/lists', $attributes)->assertStatus(422)->assertJsonValidationErrors('files');
	}

	/** @test */
	public function uploaded_files_must_have_a_valid_name()
	{
		$files = [
			UploadedFile::fake()->create('badname.txt', 1)
		];

		$attributes = LoadOrder::factory()->raw(['files' => $files]);

		$this->postJson('/api/lists', $attributes)->assertStatus(422)->assertJsonValidationErrors('files.0');
	}

	/** @test */
	public function a_guest_cannot_delete_a_list()
	{
		$list = LoadOrder::factory()->create();

		$this->deleteJson('/api/lists/' . $list->id)->assertStatus(401);
	}
}
