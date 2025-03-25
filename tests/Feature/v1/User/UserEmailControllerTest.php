<?php

declare(strict_types=1);

use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->user = User::factory()->create(['is_admin' => false]);
    $this->otherUser = User::factory()->create(['is_admin' => false]);
});

describe('update:email', function () {
    it('allows admin to update anyones email', function () {
        login($this->admin)->patchJson("/v1/users/{$this->user->name}/email", [
            'email' => 'test@example.com',
        ])->assertOk();

        $this->assertDatabaseHas('users', [
            'name' => $this->user->name,
            'email' => 'test@example.com',
        ]);
    });

    it('allows user to update their own email', function () {
        login($this->user)->patchJson("/v1/users/{$this->user->name}/email", [
            'email' => 'test2@example.com',
        ])->assertOk();

        $this->assertDatabaseHas('users', [
            'name' => $this->user->name,
            'email' => 'test2@example.com',
        ]);
    });

    it('prevents a user from updating another users email', function () {
        login($this->otherUser)->patchJson("/v1/users/{$this->user->name}/email", [
            'email' => 'test3@example.com',
        ])->assertForbidden();
    });
});

describe('destroy:email', function () {
    it('allows admin to delete anyones email', function () {
        login($this->admin)->deleteJson("/v1/users/{$this->user->name}/email")->assertNoContent();
    });

    it('allows user to delete their own email', function () {
        login($this->user)->deleteJson("/v1/users/{$this->user->name}/email")->assertNoContent();
    });

    it('prevents a user from deleting another users email', function () {
        login($this->otherUser)->deleteJson("/v1/users/{$this->user->name}/email")->assertForbidden();
    });
});
