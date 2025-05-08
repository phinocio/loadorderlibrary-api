<?php

declare(strict_types=1);

namespace Tests\Unit\Observers;

use App\Enums\v1\CacheKey;
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
    ]);
});

it('clears cache when load order is created', function () {
    Cache::put(CacheKey::LOAD_ORDERS->value, 'test-lists');

    LoadOrder::factory()->create([
        'name' => 'Another Load Order',
    ]);

    expect(Cache::has(CacheKey::LOAD_ORDERS->value))->toBeFalse();
});

it('clears cache when load order is updated', function () {
    Cache::put(CacheKey::LOAD_ORDERS->value, 'test-lists');
    Cache::put(CacheKey::LOAD_ORDER->with($this->loadOrder->slug), 'test-list-by-slug');

    $this->loadOrder->name = 'Updated Load Order';
    $this->loadOrder->save();

    expect(Cache::has(CacheKey::LOAD_ORDERS->value))->toBeFalse()
        ->and(Cache::has(CacheKey::LOAD_ORDER->with($this->loadOrder->slug)))->toBeFalse();
});

it('clears cache when load order is deleted', function () {
    Cache::put(CacheKey::LOAD_ORDERS->value, 'test-lists');
    Cache::put(CacheKey::LOAD_ORDER->with($this->loadOrder->slug), 'test-list-by-slug');

    $this->loadOrder->delete();

    expect(Cache::has(CacheKey::LOAD_ORDERS->value))->toBeFalse()
        ->and(Cache::has(CacheKey::LOAD_ORDER->with($this->loadOrder->slug)))->toBeFalse();
});
