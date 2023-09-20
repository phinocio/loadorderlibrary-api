<?php

namespace Tests\Feature;

use App\Models\LoadOrder;
use App\Models\User;
use Database\Seeders\GameSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class LoadOrderTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @test */
    public function index_returns_valid_format(): void
    {
        LoadOrder::factory(5)->create();
        $this->getJson('/v1/lists')->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
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
                    'links',
                ],
            ],
            'links',
            'meta',
        ])->assertJsonMissing(['private' => true, 'files' => true]);
    }

    /** @test */
    public function anyone_can_view_a_public_list(): void
    {
        // NOTE: Private lists are viewable by directly accessing the url, so *all* lists should be viewable.
        $loadOrder = LoadOrder::factory()->create();

        $this->getJson('/v1/lists/'.$loadOrder->slug)->assertJsonFragment([
            'name' => $loadOrder->name,
            'description' => $loadOrder->description,
        ]);
    }

    /** @test */
    public function a_guest_can_create_a_list(): void
    {
        $this->seed(GameSeeder::class);
        $this->assertGuest();
        $file = UploadedFile::fake()->createWithContent('modlist.txt', 'Fake text so it has a mimetype!');
        $attributes = [
            'name' => $this->faker->name(),
            'description' => $this->faker->paragraph(),
            'game' => $this->faker->randomDigit() + 10,
            'expires' => '3h',
            'files' => [
                $file,
            ],
        ];

        $this->postJson('/v1/lists', $attributes)->assertCreated();
        $this->assertDatabaseHas('load_orders', ['name' => $attributes['name']]);
    }

    /** @test */
    public function a_user_can_create_a_list(): void
    {
        $this->seed(GameSeeder::class);
        $user = User::factory()->create();

        $file = UploadedFile::fake()->createWithContent('modlist.txt', 'Fake text so it has a mimetype!');
        $attributes = [
            'name' => $this->faker->name(),
            'description' => $this->faker->paragraph(),
            'game' => $this->faker->randomDigit() + 10,
            'expires' => '3h',
            'files' => [
                $file,
            ],
        ];

        $this->actingAs($user)->postJson('/v1/lists', $attributes)->assertCreated();
        $this->assertDatabaseHas('load_orders', ['user_id' => $user->id]);
    }

    /** @test */
    public function a_guest_can_not_delete_a_list(): void
    {
        $loadOrder = LoadOrder::factory()->create();
        // A guest is firstly unauthorized, so we assert that before forbidden.
        $this->deleteJson('/v1/lists/'.$loadOrder->slug)->assertUnauthorized();
        $this->assertDatabaseHas('load_orders', ['slug' => $loadOrder->slug]);
    }

    /** @test */
    public function a_list_owner_can_delete_a_list(): void
    {
        $user = User::factory()->create();
        $loadOrder = LoadOrder::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->deleteJson('/v1/lists/'.$loadOrder->slug)->assertNoContent();
        $this->assertDatabaseMissing('load_orders', ['slug' => $loadOrder->slug]);
    }

    /** @test */
    public function a_user_can_not_delete_another_users_list(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $loadOrder = LoadOrder::factory()->create(['user_id' => $user->id]);

        // A user is authenticated, so we assert forbidden because they are not authorized
        $this->actingAs($user2)->deleteJson('/v1/lists/'.$loadOrder->slug)->assertForbidden();
        $this->assertDatabaseHas('load_orders', ['slug' => $loadOrder->slug]);
    }

    /** @test */
    public function a_list_being_deleted_shouldnt_break_slugs(): void
    {
        $this->seed(GameSeeder::class);
        $user = User::factory()->create();

        $file = UploadedFile::fake()->createWithContent('modlist.txt', 'Fake text so it has a mimetype!');
        $attributes = [
            'name' => 'test',
            'description' => $this->faker->paragraph(),
            'game' => $this->faker->randomDigit() + 10,
            'expires' => '3h',
            'files' => [
                $file,
            ],
        ];

        $this->actingAs($user)->postJson('/v1/lists', $attributes)->assertCreated();
        $this->actingAs($user)->postJson('/v1/lists', $attributes)->assertCreated();
        $this->actingAs($user)->postJson('/v1/lists', $attributes)->assertCreated();
        $this->actingAs($user)->deleteJson('/v1/lists/test-2')->assertNoContent();
        $this->actingAs($user)->postJson('/v1/lists', $attributes)->assertCreated();
        $this->assertDatabaseHas('load_orders', ['slug' => 'test-4']);
    }
}
