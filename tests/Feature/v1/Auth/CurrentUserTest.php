<?php

declare(strict_types=1);

use App\Models\User;

it('returns the current authenticated user', function () {
    $user = User::factory()->create()->refresh();

    login($user)
        ->getJson('/v1/me')
        ->assertOk()
        ->assertJsonStructure([
            'name',
            'email',
            'admin',
            'verified',
            'created',
            'updated',
        ])
        ->assertJson([
            'name' => $user->name,
            'email' => $user->email,
            'admin' => $user->is_admin,
            'verified' => $user->is_verified,
        ]);
});

it('returns a 401 when the user is not authenticated', function () {
    $this->assertGuest();
    $this->getJson('/v1/me')->assertUnauthorized();
});
