<?php

declare(strict_types=1);

use App\Enums\v1\CacheKey;
use App\Models\Game;
use App\Models\LoadOrder;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

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

    Storage::fake('uploads');
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

        $this->getJson('/v1/lists');
        $this->getJson('/v1/lists?query=test');

        expect($cacheKey1)->toBe(CacheKey::LOAD_ORDERS->value)
            ->and($cacheKey2)->toBe(CacheKey::LOAD_ORDERS->with(md5('query=test')));
    });
});

describe('store', function () {
    it('allows authenticated user to upload a load order with files', function () {
        $user = User::factory()->create();
        $game = Game::factory()->create();

        $response = login($user)->postJson('/v1/lists', [
            'name' => 'New Load Order',
            'description' => 'Test description',
            'version' => '1.0.0',
            'game_id' => $game->id,
            'files' => [
                UploadedFile::fake()->createWithContent('modlist.txt', "mod1\nmod2\nmod3"),
                UploadedFile::fake()->createWithContent('plugins.txt', "plugin1.esp\nplugin2.esp"),
            ],
        ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'data' => [
                    'name',
                    'version',
                    'slug',
                    'url',
                    'description',
                    'files',
                    'author',
                    'game',
                ],
            ])
            ->assertJsonPath('data.name', 'New Load Order')
            ->assertJsonPath('data.version', '1.0.0')
            ->assertJsonPath('data.description', 'Test description')
            ->assertJsonPath('data.game.name', $game->name)
            ->assertJsonPath('data.author.name', $user->name)
            ->assertJsonCount(2, 'data.files');

        $this->assertDatabaseHas('load_orders', [
            'name' => 'New Load Order',
            'version' => '1.0.0',
            'description' => 'Test description',
            'user_id' => $user->id,
            'game_id' => $game->id,
        ]);
    });

    it('allows guest to upload a load order', function () {
        $game = Game::factory()->create();

        $response = guest()->postJson('/v1/lists', [
            'name' => 'New Load Order',
            'game_id' => $game->id,
            'files' => [
                UploadedFile::fake()->createWithContent('modlist.txt', "mod1\nmod2"),
            ],
        ]);

        $response->assertCreated()->assertJsonStructure([
            'data' => [
                'name',
                'version',
                'slug',
                'url',
                'description',
                'files',
                'author',
                'game',
            ],
        ])
            ->assertJsonPath('data.name', 'New Load Order')
            ->assertJsonPath('data.game.name', $game->name)
            ->assertJsonPath('data.author.name', null)
            ->assertJsonCount(1, 'data.files');

        $this->assertDatabaseHas('load_orders', [
            'name' => 'New Load Order',
            'user_id' => null,
            'game_id' => $game->id,
        ]);
    });

    it('validates required fields for load order upload', function () {
        $user = User::factory()->create();

        $response = login($user)->postJson('/v1/lists', [
            'description' => 'Test description',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'game_id', 'files']);
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

describe('destroy', function () {
    it('allows author to delete their load order', function () {
        login($this->author)
            ->deleteJson("/v1/lists/{$this->loadOrder->slug}")
            ->assertNoContent();

        $this->assertDatabaseMissing('load_orders', [
            'slug' => $this->loadOrder->slug,
        ]);
    });

    it('prevents admin from deleting load order through regular route', function () {
        $admin = User::factory()->create(['is_admin' => true]);

        login($admin)
            ->deleteJson("/v1/lists/{$this->loadOrder->slug}")
            ->assertForbidden();
    });

    it('prevents non-author user from deleting load order', function () {
        $otherUser = User::factory()->create();

        login($otherUser)
            ->deleteJson("/v1/lists/{$this->loadOrder->slug}")
            ->assertForbidden();
        $this->assertDatabaseHas('load_orders', [
            'slug' => $this->loadOrder->slug,
        ]);
    });

    it('prevents guest from deleting load order', function () {
        guest()
            ->deleteJson("/v1/lists/{$this->loadOrder->slug}")
            ->assertUnauthorized();
        $this->assertDatabaseHas('load_orders', [
            'slug' => $this->loadOrder->slug,
        ]);
    });

    it('returns 404 when deleting non-existent load order', function () {
        login($this->author)
            ->deleteJson('/v1/lists/non-existent-load-order')
            ->assertNotFound();
    });
});
