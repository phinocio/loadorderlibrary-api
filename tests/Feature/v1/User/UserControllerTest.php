<?php

declare(strict_types=1);

use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->user = User::factory()->create(['is_admin' => false]);
    $this->otherUser = User::factory()->create(['is_admin' => false]);
});

it('only allows admin to view index', function () {
    login($this->admin)->getJson('/v1/users')->assertOk()->assertJsonStructure([
        'data' => [
            '*' => [
                'name',
                'email',
                'admin',
                'verified',
                'created',
                'updated',
            ],
        ],
    ]);

    login($this->user)->getJson('/v1/users')->assertForbidden();
});
