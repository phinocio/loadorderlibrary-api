<?php

declare(strict_types=1);

beforeEach(function () {
    $this->name = fake()->name();
    $this->password = fake()->password(8);
});

it('registers a user', function () {
    $this->postJson('/v1/register', [
        'name' => $this->name,
        'password' => $this->password,
        'password_confirmation' => $this->password,
    ])
        ->assertCreated()
        ->assertExactJsonStructure(['data' => getUserJsonStructure()])
        ->assertJsonPath('data.name', $this->name)
        ->assertJsonPath('data.email', null)
        ->assertJsonPath('data.admin', false)
        ->assertJsonPath('data.verified', false);

    $this->assertDatabaseHas('users', [
        'name' => $this->name,
    ]);

    $this->assertAuthenticated();
});

it('returns a 422 when the password confirmation does not match', function () {
    $this->postJson('/v1/register', [
        'name' => $this->name,
        'password' => $this->password,
        'password_confirmation' => 'invalid',
    ])
        ->assertUnprocessable()
        ->assertJson([
            'message' => 'The password field confirmation does not match.',
        ]);
});

it('returns a 403 when the user is already logged in', function () {
    login()->postJson('/v1/register', [
        'name' => $this->name,
        'password' => $this->password,
        'password_confirmation' => $this->password,
    ])
        ->assertForbidden()
        ->assertJson([
            'message' => 'You cannot access this route while logged in.',
        ]);
});
