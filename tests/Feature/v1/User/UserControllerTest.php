<?php

declare(strict_types=1);

use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->user = User::factory()->create(['is_admin' => false, 'email' => 'user@example.com']);
    $this->otherUser = User::factory()->create(['is_admin' => false, 'email' => 'otheruser@example.com']);
});

describe('update', function () {
    it('only allows admin or user themselves to update email', function () {
        login($this->admin)->patchJson("/v1/users/{$this->user->name}", ['email' => 'newemail@example.com'])->assertOk();
        $this->assertDatabaseHas('users', [
            'email' => 'newemail@example.com',
        ]);

        login($this->user)->patchJson("/v1/users/{$this->user->name}", ['email' => 'newemail2@example.com'])->assertOk();
        $this->assertDatabaseHas('users', [
            'email' => 'newemail2@example.com',
        ]);

        login($this->otherUser)->patchJson("/v1/users/{$this->user->name}", ['email' => 'newemail3@example.com'])->assertForbidden();
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
