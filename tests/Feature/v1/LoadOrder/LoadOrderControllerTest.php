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
            'game' => $game->id,
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
            'game' => $game->id,
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
            ->assertJsonValidationErrors(['name', 'game', 'files']);
    });

    it('rejects token without create permission', function () {
        $user = User::factory()->create();
        $game = Game::factory()->create();
        $token = $user->createToken('Read Only Token', ['read'])->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/v1/lists', [
                'name' => 'New Load Order',
                'game' => $game->id,
                'files' => [
                    UploadedFile::fake()->createWithContent('modlist.txt', "mod1\nmod2"),
                ],
            ]);

        $response->assertForbidden()
            ->assertJson([
                'message' => "This action is forbidden. (Token doesn't have permission for this action.)",
            ]);
    });

    it('rejects invalid bearer token', function () {
        $game = Game::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer invalid-token-123')
            ->postJson('/v1/lists', [
                'name' => 'New Load Order',
                'game' => $game->id,
                'files' => [
                    UploadedFile::fake()->createWithContent('modlist.txt', "mod1\nmod2"),
                ],
            ]);

        $response->assertUnauthorized()
            ->assertJson([
                'message' => 'Unauthenticated. (Make sure the token is correct.)',
            ]);
    });

    it('allows valid token with create permission', function () {
        $user = User::factory()->create();
        $game = Game::factory()->create();
        $token = $user->createToken('Full Access Token', ['create'])->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/v1/lists', [
                'name' => 'New Load Order',
                'game' => $game->id,
                'files' => [
                    UploadedFile::fake()->createWithContent('modlist.txt', "mod1\nmod2"),
                ],
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'New Load Order')
            ->assertJsonPath('data.author.name', $user->name);

        $this->assertDatabaseHas('load_orders', [
            'name' => 'New Load Order',
            'user_id' => $user->id,
            'game_id' => $game->id,
        ]);
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

describe('update', function () {
    it('allows author to update their load order', function () {
        $loadOrder = LoadOrder::factory()->create([
            'name' => 'Original Name',
            'description' => 'Original Description',
            'user_id' => $this->author->id,
        ]);

        $response = login($this->author)->patchJson("/v1/lists/{$loadOrder->slug}", [
            'name' => 'Updated Name',
            'description' => 'Updated Description',
            'files' => [
                UploadedFile::fake()->createWithContent('modlist.txt', "mod1\nmod2\nmod3"),
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Updated Name')
            ->assertJsonPath('data.description', 'Updated Description');

        $this->assertDatabaseHas('load_orders', [
            'id' => $loadOrder->id,
            'name' => 'Updated Name',
            'description' => 'Updated Description',
        ]);

        $this->assertDatabaseHas('files', [
            'clean_name' => 'modlist.txt',
        ]);

        $this->assertDatabaseHas('file_load_order', [
            'load_order_id' => $loadOrder->id,
            'file_id' => 1,
        ]);
    });

    it('allows partial updates without files', function () {
        $loadOrder = LoadOrder::factory()->create([
            'name' => 'Original Name',
            'description' => 'Original Description',
            'user_id' => $this->author->id,
        ]);

        $response = login($this->author)->patchJson("/v1/lists/{$loadOrder->slug}", [
            'name' => 'Updated Name',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Updated Name')
            ->assertJsonPath('data.description', 'Original Description');

        $this->assertDatabaseHas('load_orders', [
            'id' => $loadOrder->id,
            'name' => 'Updated Name',
            'description' => 'Original Description',
        ]);
    });

    it('prevents non-author user from updating load order', function () {
        $otherUser = User::factory()->create();

        $response = login($otherUser)->patchJson("/v1/lists/{$this->loadOrder->slug}", [
            'name' => 'Updated Name',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('load_orders', [
            'id' => $this->loadOrder->id,
            'name' => $this->loadOrder->name,
        ]);
    });

    it('prevents guest from updating load order', function () {
        $response = guest()->patchJson("/v1/lists/{$this->loadOrder->slug}", [
            'name' => 'Updated Name',
        ]);

        $response->assertUnauthorized();
        $this->assertDatabaseHas('load_orders', [
            'id' => $this->loadOrder->id,
            'name' => $this->loadOrder->name,
        ]);
    });

    it('allows author to update load order expires date', function () {
        $loadOrder = LoadOrder::factory()->create([
            'name' => 'Test Load Order',
            'expires_at' => now()->addDays(7),
            'user_id' => $this->author->id,
        ]);

        $response = login($this->author)->patchJson("/v1/lists/{$loadOrder->slug}", [
            'expires' => '1w',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Test Load Order');

        // Check that the expires date was updated in the database
        $this->assertDatabaseHas('load_orders', [
            'id' => $loadOrder->id,
            'name' => 'Test Load Order',
        ]);

        // Refresh the model and check the expires_at field
        $loadOrder->refresh();

        // Check that it's approximately one week from now (allowing 2 minutes tolerance for test execution time)
        $expectedDate = now()->addWeek();
        expect($loadOrder->expires_at)->not->toBeNull()
            ->and($loadOrder->expires_at->diffInMinutes($expectedDate))->toBeLessThan(2);
    });

    it('allows author to set load order to never expire', function () {
        $loadOrder = LoadOrder::factory()->create([
            'name' => 'Test Load Order',
            'expires_at' => now()->addDays(7),
            'user_id' => $this->author->id,
        ]);

        $response = login($this->author)->patchJson("/v1/lists/{$loadOrder->slug}", [
            'expires' => 'never',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Test Load Order');

        // Refresh the model and check the expires_at field is null
        $loadOrder->refresh();
        expect($loadOrder->expires_at)->toBeNull();
    });

    it('validates update fields', function () {
        $response = login($this->author)->patchJson("/v1/lists/{$this->loadOrder->slug}", [
            'name' => '',
            'website' => 'not-a-url',
            'discord' => 'not-a-url',
            'readme' => 'not-a-url',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'website', 'discord', 'readme']);
    });
});
