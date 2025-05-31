<?php

declare(strict_types=1);

use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();

    // Create API tokens with different abilities
    $this->readToken = $this->user->createToken('Read Token', ['read'])->plainTextToken;
    $this->createToken = $this->user->createToken('Create Token', ['create'])->plainTextToken;
});

describe('GET routes', function () {
    it('allows unauthenticated access to GET /v1/games (public route)', function () {
        $this->getJson('/v1/games')
            ->assertOk();
    });

    it('allows API tokens for GET /v1/games', function () {
        $this->withHeader('Authorization', 'Bearer '.$this->readToken)
            ->getJson('/v1/games')
            ->assertOk();
    });

    it('allows session auth for GET /v1/games', function () {
        login($this->user)
            ->getJson('/v1/games')
            ->assertOk();
    });

    it('allows unauthenticated access to GET /v1/games/{slug} (public route)', function () {
        // This might return 404 since we don't have test data, but it shouldn't be 401
        $response = $this->getJson('/v1/games/test-game');
        expect($response->status())->not->toBe(401);
    });

    it('allows API tokens for GET /v1/games/{slug}', function () {
        $response = $this->withHeader('Authorization', 'Bearer '.$this->readToken)
            ->getJson('/v1/games/test-game');
        // Should not return 401 (might be 404 if game doesn't exist)
        expect($response->status())->not->toBe(401);
    });

    it('allows session auth for GET /v1/games/{slug}', function () {
        $response = login($this->user)
            ->getJson('/v1/games/test-game');
        // Should not return 401 (might be 404 if game doesn't exist)
        expect($response->status())->not->toBe(401);
    });
});

describe('public route behavior', function () {
    it('consistently allows all authentication types on public game routes', function () {
        $routes = [
            '/v1/games',
            '/v1/games/test-slug',
        ];

        foreach ($routes as $route) {
            // Unauthenticated should work
            $response = $this->getJson($route);
            expect($response->status())->not->toBe(401, "Route {$route} should not return 401 without auth");

            // API token should work
            $response = $this->withHeader('Authorization', 'Bearer '.$this->readToken)
                ->getJson($route);
            expect($response->status())->not->toBe(401, "Route {$route} should not return 401 with token auth");

            // Session auth should work
            $response = login($this->user)->getJson($route);
            expect($response->status())->not->toBe(401, "Route {$route} should not return 401 with session auth");
        }
    });
});

describe('edge cases', function () {
    it('handles invalid tokens gracefully on public routes', function () {
        // Even with invalid tokens, public routes should work (fallback to unauthenticated)
        $response = $this->withHeader('Authorization', 'Bearer invalid-token')
            ->getJson('/v1/games');
        expect($response->status())->not->toBe(401);
    });

    it('handles malformed authorization headers on public routes', function () {
        // Even with malformed headers, public routes should work
        $response = $this->withHeader('Authorization', 'NotBearer '.$this->readToken)
            ->getJson('/v1/games');
        expect($response->status())->not->toBe(401);

        $response = $this->withHeader('Authorization', 'Bearer')
            ->getJson('/v1/games');
        expect($response->status())->not->toBe(401);
    });
});
