<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_register_with_email(): void
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

    /** @test */
    public function a_user_can_register_without_email(): void
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

    /** @test */
    public function a_user_can_not_register_with_existing_name(): void
    {
        $data = [
            'name' => fake()->name(),
            'password' => 'password',
            'password_confirmation' => 'password',
            'email' => null,
        ];

        $this->postJson('/register', $data);
        $this->postJson('/logout');
        $response = $this->postJson('/register', $data)->assertUnprocessable();
    }

    /** @test */
    public function a_user_can_login(): void
    {
        $user = User::factory()->create();

        $this->postJson('/login', ['name' => $user->name, 'password' => 'password'])->assertOk();
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function a_user_can_log_out(): void
    {
        $user = User::factory()->create();

        $this->postJson('/login', ['name' => $user->name, 'password' => 'password']);
        $this->postJson('/logout')->assertNoContent();
    }
}
