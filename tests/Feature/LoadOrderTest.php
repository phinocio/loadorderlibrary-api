<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\LoadOrder;
use App\Models\User;
use Database\Seeders\GameSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class LoadOrderTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function index_returns_valid_format(): void
    {
		$this->seed(GameSeeder::class);
		LoadOrder::factory(5)->create();
        $this->getJson('/v1/lists')->assertOk()->assertJsonStructure([
			'data' => [
				'*' =>[
					'name',
					'version',
					'slug',
					'url',
					'description',
					'private',
					'expires',
					'created',
					'updated',
					'author',
					'game',
					'files',
					'links'
				]
			],
			'links',
			'meta'
		]);
    }

	/** @test */
	public function a_guest_can_create_a_list(): void
	{
		$this->seed(GameSeeder::class);
		$this->assertGuest();
		$file = UploadedFile::fake()->create('modlist.txt', 4, 'text/plain');
		$attributes = [
			'name' => $this->faker->name(),
			'description' => $this->faker->paragraph(),
			'game' => $this->faker->randomDigit() + 10,
			'expires' => '3h',
			'files' => [
				$file
			]
		];

		$this->postJson('/v1/lists', $attributes)->assertCreated();
		$this->assertDatabaseHas('load_orders', ['name' => $attributes['name']]);
	}

	/** @test */
	public function a_user_can_create_a_list(): void
	{
		$this->seed(GameSeeder::class);
		$user = User::factory()->create();

		$file = UploadedFile::fake()->create('modlist.txt', 4, 'text/plain');
		$attributes = [
			'name' => $this->faker->name(),
			'description' => $this->faker->paragraph(),
			'game' => $this->faker->randomDigit() + 10,
			'expires' => '3h',
			'files' => [
				$file
			]
		];

		$this->actingAs($user)->postJson('/v1/lists', $attributes)->assertCreated();
		$this->assertDatabaseHas('load_orders', ['user_id' => $user->id]);
	}

	/** @test */
	public function a_guest_can_not_delete_a_list(): void
	{
		$this->seed(GameSeeder::class);
		$this->assertGuest();

		$file = UploadedFile::fake()->create('modlist.txt', 4, 'text/plain');
		$attributes = [
			'name' => $this->faker->name(),
			'description' => $this->faker->paragraph(),
			'game' => $this->faker->randomDigit() + 10,
			'expires' => '3h',
			'files' => [
				$file
			]
		];

		$resp = $this->postJson('/v1/lists', $attributes)->assertCreated();
		$list = $resp->json();
		$this->deleteJson('/v1/lists/' . $list['data']['slug'])->assertUnauthorized();
		$this->assertDatabaseHas('load_orders', ['slug' => $list['data']['slug']]);
	}

	/** @test */
	public function a_list_owner_can_delete_a_list(): void
	{
		$this->seed(GameSeeder::class);
		$user = User::factory()->create();

		$file = UploadedFile::fake()->create('modlist.txt', 4, 'text/plain');
		$attributes = [
			'name' => $this->faker->name(),
			'description' => $this->faker->paragraph(),
			'game' => $this->faker->randomDigit() + 10,
			'expires' => '3h',
			'files' => [
				$file
			]
		];

		$resp = $this->actingAs($user)->postJson('/v1/lists', $attributes)->assertCreated();
		$list = $resp->json();
		$this->deleteJson('/v1/lists/' . $list['data']['slug'])->assertNoContent();
		$this->assertDatabaseMissing('load_orders', ['slug' => $list['data']['slug']]);
	}

	/** @test */
	public function a_user_can_not_delete_another_users_list(): void
	{
		$this->seed(GameSeeder::class);
		$user = User::factory()->create();
		$user2 = User::factory()->create();

		$file = UploadedFile::fake()->create('modlist.txt', 4, 'text/plain');
		$attributes = [
			'name' => $this->faker->name(),
			'description' => $this->faker->paragraph(),
			'game' => $this->faker->randomDigit() + 10,
			'expires' => '3h',
			'files' => [
				$file
			]
		];

		$resp = $this->actingAs($user)->postJson('/v1/lists', $attributes)->assertCreated();
		$list = $resp->json();
		$this->actingAs($user2)->deleteJson('/v1/lists/' . $list['data']['slug'])->assertUnauthorized();
		$this->assertDatabaseHas('load_orders', ['slug' => $list['data']['slug']]);
	}
}
