<?php

namespace Tests\Feature;

use App\Models\LoadOrder;
use App\Models\User;
use Database\Seeders\GameSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiTokenTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @test */
    public function a_user_can_create_a_list_with_api_token(): void
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
        Sanctum::actingAs($user, ['create']);
        $this->postJson('/v1/lists', $attributes)->assertCreated();
        $this->assertDatabaseHas('load_orders', ['user_id' => $user->id]);
    }

    /** @test */
    public function a_list_owner_can_delete_a_list(): void
    {
        $user = User::factory()->create();
        $loadOrder = LoadOrder::factory()->create(['user_id' => $user->id]);
        $token = $user->createToken('meow', ['delete'])->plainTextToken;

        $this->assertGuest()->deleteJson('/v1/lists/'.$loadOrder->slug, [], ['Authorization' => "Bearer $token"])->assertNoContent();
        $this->assertDatabaseMissing('load_orders', ['slug' => $loadOrder->slug]);
    }

    /** @test */
    public function a_user_can_not_delete_another_users_list(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $loadOrder = LoadOrder::factory()->create(['user_id' => $user->id]);

        // A user is authenticated, so we assert forbidden because they are not authorized
        Sanctum::actingAs($user2, ['delete']);
        $this->deleteJson('/v1/lists/'.$loadOrder->slug)->assertForbidden();
        $this->assertDatabaseHas('load_orders', ['slug' => $loadOrder->slug]);
    }

    /** @test */
    public function a_user_can_be_deleted(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['delete']);
        $this->delete('/v1/user/'.$user->name)->assertNoContent();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /** @test */
    public function a_user_can_not_delete_another_user(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        Sanctum::actingAs($user, ['delete']);
        $this->delete('/v1/user/'.$user2->name)->assertUnauthorized();
        $this->assertDatabaseHas('users', ['id' => $user2->id]);
    }

    /** @test */
    public function a_guest_can_not_create_a_token()
    {
        $this->postJson('/v1/user/api-tokens', ['token_name' => 'test'])->assertUnauthorized();
    }
}
