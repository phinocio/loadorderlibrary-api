<?php

declare(strict_types=1);

use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->user = User::factory()->create(['is_admin' => false]);

    // Create API tokens with different abilities - even admin tokens should be rejected
    $this->readToken = $this->user->createToken('Read Token', ['read'])->plainTextToken;
    $this->writeToken = $this->user->createToken('Write Token', ['write'])->plainTextToken;
    $this->adminToken = $this->admin->createToken('Admin Token', ['admin'])->plainTextToken;
});

describe('GET routes', function () {
    it('rejects API tokens for GET /v1/admin/users (even admin tokens)', function () {
        $this->withHeader('Authorization', 'Bearer '.$this->adminToken)
            ->getJson('/v1/admin/users')
            ->assertUnauthorized();
    });

    it('rejects regular API tokens for GET /v1/admin/users', function () {
        $this->withHeader('Authorization', 'Bearer '.$this->readToken)
            ->getJson('/v1/admin/users')
            ->assertUnauthorized();
    });

    it('allows session auth for admin to GET /v1/admin/users', function () {
        login($this->admin)
            ->getJson('/v1/admin/users')
            ->assertOk();
    });

    it('prevents non-admin session users from GET /v1/admin/users', function () {
        login($this->user)
            ->getJson('/v1/admin/users')
            ->assertForbidden();
    });

    it('rejects unauthenticated requests for GET /v1/admin/users', function () {
        $this->getJson('/v1/admin/users')
            ->assertUnauthorized();
    });

    it('rejects API tokens for GET /v1/admin/users/{name}', function () {
        $this->withHeader('Authorization', 'Bearer '.$this->adminToken)
            ->getJson("/v1/admin/users/{$this->user->name}")
            ->assertUnauthorized();
    });

    it('allows session auth for admin to GET /v1/admin/users/{name}', function () {
        login($this->admin)
            ->getJson("/v1/admin/users/{$this->user->name}")
            ->assertOk();
    });
});

describe('POST routes', function () {
    it('rejects API tokens for POST /v1/admin/games (even admin tokens)', function () {
        $this->withHeader('Authorization', 'Bearer '.$this->adminToken)
            ->postJson('/v1/admin/games', [
                'name' => 'Test Game',
            ])
            ->assertUnauthorized();
    });

    it('rejects regular API tokens for POST /v1/admin/games', function () {
        $this->withHeader('Authorization', 'Bearer '.$this->writeToken)
            ->postJson('/v1/admin/games', [
                'name' => 'Test Game',
            ])
            ->assertUnauthorized();
    });

    it('allows session auth for admin to POST /v1/admin/games', function () {
        login($this->admin)
            ->postJson('/v1/admin/games', [
                'name' => 'Test Game',
            ])
            ->assertCreated();
    });

    it('prevents non-admin session users from POST /v1/admin/games', function () {
        login($this->user)
            ->postJson('/v1/admin/games', [
                'name' => 'Test Game',
            ])
            ->assertForbidden();
    });
});

describe('PATCH routes', function () {
    it('rejects API tokens for PATCH /v1/admin/users/{name}', function () {
        $this->withHeader('Authorization', 'Bearer '.$this->adminToken)
            ->patchJson("/v1/admin/users/{$this->user->name}", [
                'email' => 'admin-updated@example.com',
            ])
            ->assertUnauthorized();
    });

    it('allows session auth for admin to PATCH /v1/admin/users/{name}', function () {
        login($this->admin)
            ->patchJson("/v1/admin/users/{$this->user->name}", [
                'email' => 'admin-updated@example.com',
            ])
            ->assertOk();
    });

    it('rejects API tokens for PATCH /v1/admin/users/{name}/password', function () {
        $this->withHeader('Authorization', 'Bearer '.$this->adminToken)
            ->patchJson("/v1/admin/users/{$this->user->name}/password", [
                'password' => 'newpassword',
                'password_confirmation' => 'newpassword',
            ])
            ->assertUnauthorized();
    });

    it('allows session auth for admin to PATCH /v1/admin/users/{name}/password', function () {
        login($this->admin)
            ->patchJson("/v1/admin/users/{$this->user->name}/password", [
                'password' => 'newpassword',
                'password_confirmation' => 'newpassword',
            ])
            ->assertNoContent();
    });
});

describe('DELETE routes', function () {
    it('rejects API tokens for DELETE /v1/admin/users/{name}', function () {
        $this->withHeader('Authorization', 'Bearer '.$this->adminToken)
            ->deleteJson("/v1/admin/users/{$this->user->name}")
            ->assertUnauthorized();
    });

    it('allows session auth for admin to DELETE /v1/admin/users/{name}', function () {
        login($this->admin)
            ->deleteJson("/v1/admin/users/{$this->user->name}")
            ->assertNoContent();
    });

    it('rejects API tokens for DELETE /v1/admin/lists/{slug}', function () {
        $this->withHeader('Authorization', 'Bearer '.$this->adminToken)
            ->deleteJson('/v1/admin/lists/test-slug')
            ->assertUnauthorized();
    });
});

describe('edge cases', function () {
    it('consistently rejects all API token types on admin routes', function () {
        $routes = [
            ['GET', '/v1/admin/users'],
            ['POST', '/v1/admin/games'],
            ['GET', '/v1/admin/lists'],
        ];

        $tokens = [$this->readToken, $this->writeToken, $this->adminToken];

        foreach ($routes as [$method, $route]) {
            foreach ($tokens as $token) {
                $methodName = mb_strtolower($method).'Json';
                $this->withHeader('Authorization', 'Bearer '.$token)
                    ->$methodName($route, ['name' => 'Test Game'])
                    ->assertUnauthorized("Route {$method} {$route} should reject token auth");
            }
        }
    });

    it('handles invalid tokens on admin routes', function () {
        $this->withHeader('Authorization', 'Bearer invalid-token')
            ->getJson('/v1/admin/users')
            ->assertUnauthorized();
    });

    it('handles malformed authorization headers on admin routes', function () {
        $this->withHeader('Authorization', 'NotBearer '.$this->adminToken)
            ->getJson('/v1/admin/users')
            ->assertUnauthorized();

        $this->withHeader('Authorization', 'Bearer')
            ->getJson('/v1/admin/users')
            ->assertUnauthorized();
    });
});
