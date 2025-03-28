<?php

declare(strict_types=1);

use App\Models\User;

it('returns the current authenticated user', function () {
    $user = User::factory()->create()->fresh();

    login($user)
        ->getJson('/v1/me')
        ->assertOk()
        ->assertExactJsonStructure(['data' => getUserJsonStructure()]);
});

it('returns a 401 when the user is not authenticated', function () {
    $this->assertGuest();
    $this->getJson('/v1/me')->assertUnauthorized();
});
