<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->name = fake()->name();
    $this->password = fake()->password();

    $this->user = User::factory()->create([
        'name' => $this->name,
        'password' => Hash::make($this->password),
    ])->fresh();
});

it('logs in a user with valid credentials', function () {
    $this->postJson('/v1/login', [
        'name' => $this->name,
        'password' => $this->password,
    ])
        ->assertOk()
        ->assertExactJsonStructure(['data' => getCurrentUserJsonStructure()])
        ->assertJsonPath('data.name', $this->user->name)
        ->assertJsonPath('data.email', $this->user->email)
        ->assertJsonPath('data.admin', $this->user->is_admin)
        ->assertJsonPath('data.verified', $this->user->is_verified);

    $this->assertAuthenticatedAs($this->user);
});

it('returns a 401 when invalid credentials are provided', function () {
    $this->postJson('/v1/login', [
        'name' => $this->name,
        'password' => 'invalid',
    ])->assertUnauthorized()->assertJson([
        'message' => 'Invalid credentials',
    ]);
});

it('returns a 403 status code when the user is already logged in', function () {
    login($this->user)->postJson('/v1/login', [
        'name' => $this->name,
        'password' => $this->password,
    ])->assertForbidden()->assertJson([
        'message' => 'You cannot access this route while logged in.',
    ]);
});

it('remembers the user when remember me is enabled', function () {
    $response = $this->postJson('/v1/login', [
        'name' => $this->name,
        'password' => $this->password,
        'remember' => true,
    ])->assertOk();

    $this->assertAuthenticatedAs($this->user);

    // Check that the remember me cookie starts with remember_web_
    $cookies = $response->headers->getCookies();
    $this->assertTrue(collect($cookies)->contains(fn ($cookie) => str_starts_with($cookie->getName(), 'remember_web_')));
});
