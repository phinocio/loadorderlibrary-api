<?php

declare(strict_types=1);

use App\Models\Game;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();

    // Create API tokens with different abilities
    $this->readToken = $this->user->createToken('Read Token', ['read'])->plainTextToken;
    $this->createToken = $this->user->createToken('Create Token', ['create'])->plainTextToken;
    $this->updateToken = $this->user->createToken('Update Token', ['update'])->plainTextToken;
    $this->deleteToken = $this->user->createToken('Delete Token', ['delete'])->plainTextToken;
});

describe('GET routes', function () {
    it('allows unauthenticated access to GET /v1/lists (public route)', function () {
        $this->getJson('/v1/lists')
            ->assertOk();
    });

    it('allows API tokens for GET /v1/lists', function () {
        $this->withHeader('Authorization', 'Bearer '.$this->readToken)
            ->getJson('/v1/lists')
            ->assertOk();
    });

    it('allows session auth for GET /v1/lists', function () {
        login($this->user)
            ->getJson('/v1/lists')
            ->assertOk();
    });

    it('allows unauthenticated access to GET /v1/lists/{slug} (public route)', function () {
        // This might return 404 since we don't have test data, but it shouldn't be 401
        $response = $this->getJson('/v1/lists/test-slug');
        expect($response->status())->not->toBe(401);
    });

    it('allows API tokens for GET /v1/lists/{slug}', function () {
        $response = $this->withHeader('Authorization', 'Bearer '.$this->readToken)
            ->getJson('/v1/lists/test-slug');
        expect($response->status())->not->toBe(401);
    });

    it('allows session auth for GET /v1/lists/{slug}', function () {
        $response = login($this->user)
            ->getJson('/v1/lists/test-slug');
        expect($response->status())->not->toBe(401);
    });
});

describe('POST routes', function () {
    it('allows API tokens for POST /v1/lists (public route)', function () {
        $game = Game::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer '.$this->createToken)
            ->postJson('/v1/lists', [
                'name' => 'Test List',
                'description' => 'Test Description',
                'game_id' => $game->id,
                'files' => [],
            ]);
        // Should not return 401 (public route, guests allowed)
        expect($response->status())->not->toBe(401);
    });

    it('allows session auth for POST /v1/lists (public route)', function () {
        $game = Game::factory()->create();

        $response = login($this->user)
            ->postJson('/v1/lists', [
                'name' => 'Test List',
                'description' => 'Test Description',
                'game_id' => $game->id,
                'files' => [],
            ]);
        expect($response->status())->not->toBe(401);
    });

    it('allows unauthenticated requests for POST /v1/lists (public route)', function () {
        $game = Game::factory()->create();

        $response = $this->postJson('/v1/lists', [
            'name' => 'Test Load Order',
            'description' => 'Test Description',
            'game_id' => $game->id,
            'files' => [],
        ]);

        // Should not return 401 - guests are allowed to create lists
        expect($response->status())->not->toBe(401);
    });
});

describe('PATCH routes', function () {
    it('allows API tokens for PATCH /v1/lists/{slug} (auth:sanctum middleware)', function () {
        $response = $this->withHeader('Authorization', 'Bearer '.$this->updateToken)
            ->patchJson('/v1/lists/test-slug', [
                'name' => 'Updated List',
            ]);
        // Should not return 401 (might return 404 if list doesn't exist)
        expect($response->status())->not->toBe(401);
    });

    it('allows session auth for PATCH /v1/lists/{slug}', function () {
        $response = login($this->user)
            ->patchJson('/v1/lists/test-slug', [
                'name' => 'Updated List',
            ]);
        expect($response->status())->not->toBe(401);
    });

    it('rejects unauthenticated requests for PATCH /v1/lists/{slug}', function () {
        $this->patchJson('/v1/lists/test-slug', [
            'name' => 'Updated List',
        ])
            ->assertUnauthorized();
    });
});

describe('DELETE routes', function () {
    it('allows API tokens for DELETE /v1/lists/{slug} (auth:sanctum middleware)', function () {
        $response = $this->withHeader('Authorization', 'Bearer '.$this->deleteToken)
            ->deleteJson('/v1/lists/test-slug');
        // Should not return 401 (might return 404 if list doesn't exist)
        expect($response->status())->not->toBe(401);
    });

    it('allows session auth for DELETE /v1/lists/{slug}', function () {
        $response = login($this->user)
            ->deleteJson('/v1/lists/test-slug');
        expect($response->status())->not->toBe(401);
    });

    it('rejects unauthenticated requests for DELETE /v1/lists/{slug}', function () {
        $this->deleteJson('/v1/lists/test-slug')
            ->assertUnauthorized();
    });
});
