<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_register_with_email(): void
    {
        $data = [
            'name' => fake()->name(),
            'password' => 'password',
            'password_confirmation' => 'password',
            'email' => fake()->email(),
        ];

        $this->postJson('/register', $data)->assertCreated();

        $this->assertDatabaseHas('users', ['name' => $data['name']]);
    }

    public function test_a_user_can_register_without_email(): void
    {
        $data = [
            'name' => fake()->name(),
            'password' => 'password',
            'password_confirmation' => 'password',
            'email' => null,
        ];

        $this->postJson('/register', $data)->assertCreated();

        $this->assertDatabaseHas('users', ['name' => $data['name'], 'email' => null]);
    }

    public function test_a_user_can_not_register_with_existing_name(): void
    {
        $data = [
            'name' => fake()->name(),
            'password' => 'password',
            'password_confirmation' => 'password',
            'email' => null,
        ];

        $this->postJson('/register', $data);
        $this->postJson('/logout');
        $this->postJson('/register', $data)->assertUnprocessable();
    }

    public function test_a_user_can_login(): void
    {
        $user = User::factory()->create();

        $this->postJson('/login', ['name' => $user->name, 'password' => 'password'])->assertOk();
        $this->assertAuthenticatedAs($user);
    }

    public function test_a_user_can_log_out(): void
    {
        $user = User::factory()->create();

        $this->postJson('/login', ['name' => $user->name, 'password' => 'password']);
        $this->postJson('/logout')->assertNoContent();
    }

    public function test_a_user_can_be_deleted(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->delete('/v1/user/'.$user->name)->assertNoContent();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_a_user_can_not_delete_another_user(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $this->actingAs($user)->delete('/v1/user/'.$user2->name)->assertForbidden();
        $this->assertDatabaseHas('users', ['id' => $user2->id]);
    }
}
