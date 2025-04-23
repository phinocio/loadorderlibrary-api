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
        guest()->getJson("/v1/users/{$this->user->name}/profile")
            ->assertOk()
            ->assertExactJsonStructure(['data' => getUserWithProfileJsonStructure()]);

        // Admin can view profile
        login($this->admin)->getJson("/v1/users/{$this->user->name}/profile")
            ->assertOk()
            ->assertExactJsonStructure(['data' => getUserWithProfileJsonStructure()]);

        // Other user can view profile
        login($this->otherUser)->getJson("/v1/users/{$this->user->name}/profile")
            ->assertOk()
            ->assertExactJsonStructure(['data' => getUserWithProfileJsonStructure()]);
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
            ->assertExactJsonStructure(['data' => getCurrentUserJsonStructure()]);

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
