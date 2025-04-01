<?php

declare(strict_types=1);

use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->user = User::factory()->create()->fresh();
    $this->otherUser = User::factory()->create()->fresh();
});

describe('show', function () {
    it('allows anyone to view a user profile', function () {
        // Unauthenticated user can view profile
        $this->getJson("/v1/users/{$this->user->name}/profile")
            ->assertOk()
            ->assertExactJsonStructure(['data' => getUserJsonStructure()]);

        // Admin can view profile
        login($this->admin)->getJson("/v1/users/{$this->user->name}/profile")
            ->assertOk()
            ->assertExactJsonStructure(['data' => getUserJsonStructure()]);

        // Other user can view profile
        login($this->otherUser)->getJson("/v1/users/{$this->user->name}/profile")
            ->assertOk()
            ->assertExactJsonStructure(['data' => getUserJsonStructure()]);
    });
});

describe('update', function () {
    it('does not allow admin to update', function () {
        login($this->admin)->patchJson("/v1/users/{$this->user->name}/profile", [
            'bio' => 'Test bio',
        ])
            ->assertForbidden();
    });

    it('allows owner to update', function () {
        login($this->user)->patchJson("/v1/users/{$this->user->name}/profile", [
            'bio' => 'Test bio',
        ])
            ->assertOk()
            ->assertExactJsonStructure(['data' => getUserJsonStructure()]);

        $this->assertDatabaseHas('user_profiles', [
            'bio' => 'Test bio',
        ]);
    });

    it('does not allow other user to update', function () {
        login($this->otherUser)->patchJson("/v1/users/{$this->user->name}/profile", [
            'bio' => 'Test bio',
        ])
            ->assertForbidden();
    });

    it('returns a 401 when the user is not authenticated', function () {
        $this->patchJson("/v1/users/{$this->user->name}/profile", [
            'bio' => 'Test bio',
        ])
            ->assertUnauthorized();
    });

});
