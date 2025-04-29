<?php

declare(strict_types=1);

use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->user = User::factory()->create(['is_admin' => false]);
});

describe('store', function () {
    it('allows admin to create a new game', function () {
        login($this->admin)
            ->postJson('/v1/admin/games', [
                'name' => 'New Game',
            ])
            ->assertCreated()
            ->assertExactJson([
                'data' => [
                    'id' => 1,
                    'name' => 'New Game',
                    'slug' => 'new-game',
                    'lists_count' => 0,
                ],
            ]);

        $this->assertDatabaseHas('games', [
            'name' => 'New Game',
            'slug' => 'new-game',
        ]);
    });

    it('prevents non-admin user from creating a game', function () {
        login($this->user)
            ->postJson('/v1/admin/games', [
                'name' => 'New Game',
            ])
            ->assertForbidden();
    });

    it('prevents guest from creating a game', function () {
        guest()
            ->postJson('/v1/admin/games', [
                'name' => 'New Game',
            ])
            ->assertUnauthorized();
    });

    it('validates required fields', function () {
        login($this->admin)
            ->postJson('/v1/admin/games', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    });

    it('validates name is a string', function () {
        login($this->admin)
            ->postJson('/v1/admin/games', [
                'name' => ['not', 'a', 'string'],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    });
});
