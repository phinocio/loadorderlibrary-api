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
    ])->refresh();
});

it('logs in a user with valid credentials', function () {
    $this->postJson('/v1/login', [
        'name' => $this->name,
        'password' => $this->password,
    ])
        ->assertOk()
        ->assertJsonStructure([
            'id',
            'name',
            'email',
            'admin',
            'verified',
            'created',
            'updated',
        ]);

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
