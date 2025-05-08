<?php

declare(strict_types=1);

use App\Models\Game;
use App\Models\LoadOrder;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    $this->game = Game::factory()->create(['name' => 'Test Game']);
    $this->author = User::factory()->create(['name' => 'Test Author']);
    $this->loadOrder = LoadOrder::factory()->create([
        'name' => 'Test Load Order',
        'slug' => 'test-load-order',
        'game_id' => $this->game->id,
        'user_id' => $this->author->id,
        'is_private' => false,
    ])->fresh();
});

describe('index', function () {
    it('returns a list of public load orders', function () {
        // Create a mix of public and private load orders
        LoadOrder::factory()->count(2)->public()->create();
        LoadOrder::factory()->count(3)->private()->create();

        $response = $this->getJson('/v1/lists');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'name',
                        'version',
                        'slug',
                        'url',
                        'description',
                        'website',
                        'discord',
                        'readme',
                        'private',
                        'expires',
                        'created',
                        'updated',
                        'author',
                        'game',
                        'links',
                    ],
                ],
            ])
            ->assertJsonCount(3, 'data'); // 2 public + 1 from beforeEach
    });

    it('uses different cache keys for different queries', function () {
        $cacheKey1 = null;
        $cacheKey2 = null;

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

        $this->getJson('/v1/lists');
        $this->getJson('/v1/lists?query=test');

        expect($cacheKey1)->not->toBe($cacheKey2);
    });
});

describe('show', function () {
    it('returns a specific load order by slug', function () {
        $response = $this->getJson("/v1/lists/{$this->loadOrder->slug}");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'name',
                    'version',
                    'slug',
                    'url',
                    'description',
                    'website',
                    'discord',
                    'readme',
                    'private',
                    'expires',
                    'created',
                    'updated',
                    'author',
                    'game',
                    'files',
                    'links',
                ],
            ])
            ->assertJsonPath('data.name', $this->loadOrder->name)
            ->assertJsonPath('data.slug', $this->loadOrder->slug);
    });

    it('returns 404 when load order does not exist', function () {
        $this->getJson('/v1/lists/nonexistent')->assertNotFound();
    });

    it('loads related game, author and files', function () {
        $response = $this->getJson("/v1/lists/{$this->loadOrder->slug}");

        $response->assertOk()
            ->assertJsonPath('data.game.name', $this->game->name)
            ->assertJsonPath('data.author.name', $this->author->name)
            ->assertJsonStructure([
                'data' => [
                    'files' => [],
                ],
            ]);
    });
});
