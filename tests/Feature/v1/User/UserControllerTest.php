<?php

declare(strict_types=1);

use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->user = User::factory()->create(['is_admin' => false, 'email' => 'user@example.com']);
    $this->otherUser = User::factory()->create(['is_admin' => false, 'email' => 'otheruser@example.com']);
});

describe('update', function () {
    it('allows user to update themselves', function () {
        login($this->user)
            ->patchJson("/v1/users/{$this->user->name}", ['email' => 'newemail2@example.com'])
            ->assertOk()
            ->assertExactJsonStructure(['data' => getCurrentUserJsonStructure()]);

        $this->assertDatabaseHas('users', [
            'email' => 'newemail2@example.com',
        ]);
    });

    // Admin must use Admin routes
    it('prevents admin from updating a user', function () {
        login($this->admin)
            ->patchJson("/v1/users/{$this->user->name}", ['email' => 'newemail@example.com'])
            ->assertForbidden();
    });

    it('prevents a user from updating another user', function () {
        login($this->otherUser)->patchJson("/v1/users/{$this->user->name}", ['email' => 'newemail3@example.com'])->assertForbidden();
    });

    it('prevents a guest from updating a user', function () {
        guest()->patchJson("/v1/users/{$this->user->name}", ['email' => 'newemail3@example.com'])->assertUnauthorized();
    });
});

describe('destroy', function () {
    it('allows user to delete themselves', function () {
        login($this->user)->deleteJson("/v1/users/{$this->user->name}")->assertNoContent();
        $this->assertDatabaseMissing('users', [
            'name' => $this->user->name,
        ]);
    });

    // Admin must use Admin routes
    it('prevents admin from deleting a user', function () {
        login($this->admin)->deleteJson("/v1/users/{$this->user->name}")->assertForbidden();
    });

    it('prevents a user from deleting another user', function () {
        login($this->otherUser)->deleteJson("/v1/users/{$this->user->name}")->assertForbidden();
    });

    it('prevents a guest from deleting a user', function () {
        guest()->deleteJson("/v1/users/{$this->user->name}")->assertUnauthorized();
    });
});
