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

it('only allows admin or user themselves to view show', function () {
    login($this->admin)->getJson("/v1/users/{$this->user->name}")->assertOk();
    login($this->user)->getJson("/v1/users/{$this->user->name}")->assertOk();
    login($this->otherUser)->getJson("/v1/users/{$this->user->name}")->assertForbidden();
});

it('only allows admin or user themselves to update', function () {
    login($this->admin)->putJson("/v1/users/{$this->user->name}", [
        'email' => 'test@test.com',
    ])->assertOk();

    $this->assertDatabaseHas('users', [
        'name' => $this->user->name,
        'email' => 'test@example.com',
    ]);

    login($this->user)->putJson("/v1/users/{$this->user->name}", [
        'email' => 'test2@example.com',
    ])->assertOk();

    $this->assertDatabaseHas('users', [
        'name' => $this->user->name,
        'email' => 'test2@example.com',
    ]);

    login($this->otherUser)->putJson("/v1/users/{$this->user->name}")->assertForbidden();
});
