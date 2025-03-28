<?php

declare(strict_types=1);

use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->user = User::factory()->create(['is_admin' => false]);
    $this->otherUser = User::factory()->create(['is_admin' => false]);
});

describe('index', function () {
    it('only allows admin to view index', function () {
        login($this->admin)->getJson('/v1/users')->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
                    'name',
                    'email',
                    'admin',
                    'verified',
                    'bio',
                    'discord',
                    'kofi',
                    'patreon',
                    'website',
                    'created',
                    'updated',
                ],
            ],
        ]);

        login($this->user)->getJson('/v1/users')->assertForbidden();
    });
});

describe('show', function () {
    it('only allows admin or user themselves to view show', function () {
        login($this->admin)->getJson("/v1/users/{$this->user->name}")->assertOk()->assertJsonStructure([
            'name',
            'email',
            'admin',
            'verified',
            'bio',
            'discord',
            'kofi',
            'patreon',
            'website',
            'created',
            'updated',
        ]);

        login($this->user)->getJson("/v1/users/{$this->user->name}")->assertOk()->assertJsonStructure([
            'name',
            'email',
            'admin',
            'verified',
            'bio',
            'discord',
            'kofi',
            'patreon',
            'website',
            'created',
            'updated',
        ]);

        login($this->otherUser)->getJson("/v1/users/{$this->user->name}")->assertForbidden();
    });
});

describe('update', function () {
    it('only allows admin or user themselves to update', function () {
        login($this->admin)->patchJson("/v1/users/{$this->user->name}", ['bio' => 'new bio', 'discord' => 'new discord'])->assertOk()->assertJsonStructure([
            'name',
            'email',
            'admin',
            'verified',
            'bio',
            'discord',
            'kofi',
            'patreon',
            'website',
            'created',
            'updated',
        ]);

        $this->assertDatabaseHas('users', [
            'bio' => 'new bio',
            'discord' => 'new discord',
        ]);

        login($this->user)->patchJson("/v1/users/{$this->user->name}", ['bio' => 'new bio', 'discord' => 'new discord'])->assertOk()->assertJsonStructure([
            'name',
            'email',
            'admin',
            'verified',
            'bio',
            'discord',
            'kofi',
            'patreon',
            'website',
            'created',
            'updated',
        ]);

        $this->assertDatabaseHas('users', [
            'bio' => 'new bio',
            'discord' => 'new discord',
        ]);

        login($this->otherUser)->patchJson("/v1/users/{$this->user->name}", ['bio' => 'new bio', 'discord' => 'new discord'])->assertForbidden();
    });
});

describe('destroy', function () {
    it('allows admin to delete anyone', function () {
        login($this->admin)->deleteJson("/v1/users/{$this->user->name}")->assertNoContent();

        $this->assertDatabaseMissing('users', [
            'name' => $this->user->name,
        ]);
    });

    it('allows user to delete themselves', function () {
        login($this->user)->deleteJson("/v1/users/{$this->user->name}")->assertNoContent();

        $this->assertDatabaseMissing('users', [
            'name' => $this->user->name,
        ]);
    });

    it('prevents a user from deleting another user', function () {
        login($this->otherUser)->deleteJson("/v1/users/{$this->user->name}")->assertForbidden();
    });
});
