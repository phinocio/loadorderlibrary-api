<?php

declare(strict_types=1);

use App\Models\User;

it('logs out a user', function () {
    $user = User::factory()->create()->fresh();

    login($user)->postJson('/v1/logout')->assertNoContent();

    $this->assertGuest();
});

it('returns a 401 when the user is not logged in', function () {
    $this->assertGuest();
    $this->postJson('/v1/logout')->assertUnauthorized();
});
