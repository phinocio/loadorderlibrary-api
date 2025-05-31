<?php

declare(strict_types=1);

use App\Models\Game;
use App\Models\LoadOrder;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->user = User::factory()->create()->fresh();
    $this->otherUser = User::factory()->create()->fresh();

    // Set up test data for private list visibility tests
    $this->game = Game::factory()->create();

    // Create public and private lists for the user
    $this->publicList = LoadOrder::factory()->create([
        'name' => 'Public List',
        'user_id' => $this->user->id,
        'game_id' => $this->game->id,
        'is_private' => false,
    ]);

    $this->privateList = LoadOrder::factory()->create([
        'name' => 'Private List',
        'user_id' => $this->user->id,
        'game_id' => $this->game->id,
        'is_private' => true,
    ]);
});

describe('show', function () {
    it('allows anyone to view a user profile', function () {
        // Unauthenticated user can view profile
        guest()->getJson("/v1/users/{$this->user->name}/profile")
            ->assertOk()
            ->assertExactJsonStructure(['data' => getUserWithProfileJsonStructure(true)]);

        // Admin can view profile
        login($this->admin)->getJson("/v1/users/{$this->user->name}/profile")
            ->assertOk()
            ->assertExactJsonStructure(['data' => getUserWithProfileJsonStructure(true)]);

        // Other user can view profile
        login($this->otherUser)->getJson("/v1/users/{$this->user->name}/profile")
            ->assertOk()
            ->assertExactJsonStructure(['data' => getUserWithProfileJsonStructure(true)]);
    });

    it('shows only public lists when viewing another user profile', function () {
        $response = login($this->otherUser)->getJson("/v1/users/{$this->user->name}/profile");

        $response->assertOk()
            ->assertJsonCount(1, 'data.lists')
            ->assertJsonPath('data.lists.0.name', 'Public List');
    });

    it('shows only public lists to unauthenticated users viewing a profile', function () {
        $response = guest()->getJson("/v1/users/{$this->user->name}/profile");

        $response->assertOk()
            ->assertJsonCount(1, 'data.lists')
            ->assertJsonPath('data.lists.0.name', 'Public List');
    });

    it('shows only public lists to admin users viewing a profile', function () {
        $response = login($this->admin)->getJson("/v1/users/{$this->user->name}/profile");

        $response->assertOk()
            ->assertJsonCount(1, 'data.lists')
            ->assertJsonPath('data.lists.0.name', 'Public List');
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
