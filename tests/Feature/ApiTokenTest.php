<?php

namespace Tests\Feature;

use App\Models\LoadOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiTokenTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_a_user_can_create_a_list_with_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('meow', ['create'])->plainTextToken;

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

        $this->assertGuest()->postJson('/v1/lists', $attributes, ['Authorization' => "Bearer $token"])->assertCreated();
        $this->assertDatabaseHas('load_orders', ['user_id' => $user->id]);
    }

    public function test_a_list_owner_can_delete_a_list_with_token(): void
    {
        $user = User::factory()->create();
        $loadOrder = LoadOrder::factory()->create(['user_id' => $user->id]);
        $token = $user->createToken('meow', ['delete'])->plainTextToken;

        $this->assertGuest()->deleteJson('/v1/lists/'.$loadOrder->slug, [], ['Authorization' => "Bearer $token"])->assertNoContent();
        $this->assertDatabaseMissing('load_orders', ['slug' => $loadOrder->slug]);
    }

    public function test_a_user_can_not_delete_another_users_list(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $loadOrder = LoadOrder::factory()->create(['user_id' => $user->id]);
        $token = $user2->createToken('meow', ['delete'])->plainTextToken;

        // A user is authenticated, so we assert forbidden because they are not authorized
        $this->assertGuest()->deleteJson('/v1/lists/'.$loadOrder->slug, [], ['Authorization' => "Bearer $token"])->assertForbidden();
        $this->assertDatabaseHas('load_orders', ['slug' => $loadOrder->slug]);
    }

    public function test_a_user_can_not_be_deleted_by_a_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('meow', ['delete'])->plainTextToken;

        $this->assertGuest()->deleteJson('/v1/user/'.$user->name, [], ['Authorization' => "Bearer $token"])->assertUnauthorized();
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_a_user_can_not_delete_another_user(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $token = $user->createToken('meow', ['delete'])->plainTextToken;

        $this->assertGuest()->deleteJson('/v1/user/'.$user2->name, [], ['Authorization' => "Bearer $token"])->assertUnauthorized();
        $this->assertDatabaseHas('users', ['id' => $user2->id]);
    }

    public function test_a_guest_can_not_create_a_token(): void
    {
        $this->postJson('/v1/user/api-tokens', ['token_name' => 'test'])->assertUnauthorized();
    }
}
