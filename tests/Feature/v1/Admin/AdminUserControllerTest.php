<?php

declare(strict_types=1);

use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->user = User::factory()->create(['is_admin' => false, 'email' => 'user@example.com']);
    $this->otherUser = User::factory()->create(['is_admin' => false, 'email' => 'otheruser@example.com']);
});

describe('index', function () {
    it('only allows admin to view index', function () {
        login($this->admin)->getJson('/v1/admin/users')->assertOk()->assertExactJsonStructure([
            'data' => [
                '*' => getUserJsonStructure(), // no profile for list of users
            ],
        ]);

        login($this->user)->getJson('/v1/admin/users')->assertForbidden();
    });
});

describe('update', function () {
    it('only allows admin to update user email', function () {
        login($this->admin)->patchJson("/v1/admin/users/{$this->user->name}", ['email' => 'newemail@example.com'])->assertOk();
        $this->assertDatabaseHas('users', [
            'email' => 'newemail@example.com',
        ]);

        login($this->user)->patchJson("/v1/admin/users/{$this->user->name}", ['email' => 'newemail2@example.com'])->assertForbidden();
    });
});

describe('destroy', function () {
    it('allows admin to delete users', function () {
        login($this->admin)->deleteJson("/v1/admin/users/{$this->user->name}")->assertNoContent();

        $this->assertDatabaseMissing('users', [
            'name' => $this->user->name,
        ]);
    });

    it('prevents a non-admin user from deleting users', function () {
        login($this->user)->deleteJson("/v1/admin/users/{$this->otherUser->name}")->assertForbidden();
    });
});
