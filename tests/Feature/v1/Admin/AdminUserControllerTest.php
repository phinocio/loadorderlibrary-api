<?php

declare(strict_types=1);

use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->user = User::factory()->create(['is_admin' => false, 'email' => 'user@example.com']);
    $this->otherUser = User::factory()->create(['is_admin' => false, 'email' => 'otheruser@example.com']);
});

describe('index', function () {
    it('allows admin to view index', function () {
        login($this->admin)->getJson('/v1/admin/users')->assertOk()->assertExactJsonStructure([
            'data' => [
                '*' => getUserJsonStructure(), // no profile for list of users
            ],
        ]);
    });

    it('prevents a non-admin user from viewing index', function () {
        login($this->user)->getJson('/v1/admin/users')->assertForbidden();
    });

    it('prevents a guest from viewing index', function () {
        guest()->getJson('/v1/admin/users')->assertUnauthorized();
    });
});

describe('update', function () {
    it('allows admin to update user basic info', function () {
        login($this->admin)->patchJson("/v1/admin/users/{$this->user->name}", ['email' => 'newemail@example.com'])->assertOk();
        $this->assertDatabaseHas('users', [
            'email' => 'newemail@example.com',
        ]);
    });

    it('prevents a non-admin user from updating user', function () {
        login($this->user)->patchJson("/v1/admin/users/{$this->user->name}", ['email' => 'newemail2@example.com'])->assertForbidden();
    });

    it('prevents a guest from updating user', function () {
        guest()->patchJson("/v1/admin/users/{$this->user->name}", ['email' => 'newemail2@example.com'])->assertUnauthorized();
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

    it('prevents a guest from deleting users', function () {
        guest()->deleteJson("/v1/admin/users/{$this->otherUser->name}")->assertUnauthorized();
    });
});
