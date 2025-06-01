<?php

declare(strict_types=1);

use App\Enums\v1\CacheKey;
use App\Models\Game;
use App\Models\LoadOrder;
use App\Models\User;

beforeEach(function () {
    $this->game = Game::factory()->create(['name' => 'Test Game']);
    $this->author = User::factory()->create(['name' => 'Test Author']);
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->loadOrder = LoadOrder::factory()->create([
        'name' => 'Test Load Order',
        'slug' => 'test-load-order',
        'game_id' => $this->game->id,
        'user_id' => $this->author->id,
        'is_private' => false,
        'created_at' => now(),
    ])->fresh();
    $this->loadOrder2 = LoadOrder::factory()->create([
        'name' => 'Another Load Order',
        'slug' => 'another-load-order',
        'game_id' => $this->game->id,
        'user_id' => $this->author->id,
        'is_private' => true,
        'created_at' => now()->addSeconds(1),
    ])->fresh();
});

describe('index', function () {
    it('allows admin to view all load orders', function () {
        login($this->admin)
            ->getJson('/v1/admin/lists')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.slug', $this->loadOrder2->slug)
            ->assertJsonPath('data.1.private', false);

    });

    it('prevents non-admin from viewing load orders', function () {
        login($this->author)
            ->getJson('/v1/admin/lists')
            ->assertForbidden();
    });

    it('prevents guest from viewing load orders', function () {
        guest()
            ->getJson('/v1/admin/lists')
            ->assertUnauthorized();
    });

    it('uses different cache keys for different queries', function () {
        $cacheKey1 = null;
        $cacheKey2 = null;

        Cache::shouldReceive('tags')
            ->with([CacheKey::LOAD_ORDERS->value])
            ->twice()
            ->andReturnSelf();

        Cache::shouldReceive('rememberForever')
            ->twice()
            ->withArgs(function ($key) use (&$cacheKey1, &$cacheKey2) {
                if (! $cacheKey1) {
                    $cacheKey1 = $key;
                } else {
                    $cacheKey2 = $key;
                }

                return true;
            })
            ->andReturn(collect([]));

        login($this->admin)->getJson('/v1/admin/lists')->assertOk();
        login($this->admin)->getJson('/v1/admin/lists?query=test')->assertOk();

        expect($cacheKey1)->toBe(CacheKey::LOAD_ORDERS->value)
            ->and($cacheKey2)->toBe(CacheKey::LOAD_ORDERS->with(md5('query=test'), 'with-private'));
    });
});

describe('destroy', function () {
    it('allows admin to delete any load order', function () {
        login($this->admin)
            ->deleteJson("/v1/admin/lists/{$this->loadOrder->slug}")
            ->assertNoContent();

        $this->assertDatabaseMissing('load_orders', [
            'slug' => $this->loadOrder->slug,
        ]);
    });

    it('prevents non-admin from deleting load order', function () {
        login($this->author)
            ->deleteJson("/v1/admin/lists/{$this->loadOrder->slug}")
            ->assertForbidden();
    });

    it('prevents guest from deleting load order', function () {
        guest()
            ->deleteJson("/v1/admin/lists/{$this->loadOrder->slug}")
            ->assertUnauthorized();
    });

    it('returns 404 when deleting non-existent load order', function () {
        login($this->admin)
            ->deleteJson('/v1/admin/lists/non-existent-load-order')
            ->assertNotFound();
    });
});
