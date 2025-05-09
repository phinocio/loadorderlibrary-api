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

it('clears only load orders cache tag when load order is created', function () {
    // Set up multiple cache entries with query strings
    Cache::tags([CacheKey::LOAD_ORDERS->value])->put('test-lists', 'value1');
    Cache::tags([CacheKey::LOAD_ORDERS->value])->put(CacheKey::LOAD_ORDERS->with(md5('query=test&page[size]=10')), 'value2');
    Cache::tags([CacheKey::LOAD_ORDERS->value])->put(CacheKey::LOAD_ORDERS->with(md5('game=skyrim')), 'value3');
    Cache::put(CacheKey::LOAD_ORDER->with('other-slug'), 'value4');

    LoadOrder::factory()->create([
        'name' => 'Another Load Order',
    ]);

    // All tagged caches should be cleared
    expect(Cache::tags([CacheKey::LOAD_ORDERS->value])->get('test-lists'))->toBeNull()
        ->and(Cache::tags([CacheKey::LOAD_ORDERS->value])->get(CacheKey::LOAD_ORDERS->with(md5('query=test&page[size]=10'))))->toBeNull()
        ->and(Cache::tags([CacheKey::LOAD_ORDERS->value])->get(CacheKey::LOAD_ORDERS->with(md5('game=skyrim'))))->toBeNull()
        // But individual load order caches should remain
        ->and(Cache::get(CacheKey::LOAD_ORDER->with('other-slug')))->toBe('value4');
});

it('clears cache when load order is updated', function () {
    // Set up multiple cache entries with query strings
    Cache::tags([CacheKey::LOAD_ORDERS->value])->put('test-lists', 'value1');
    Cache::tags([CacheKey::LOAD_ORDERS->value])->put(CacheKey::LOAD_ORDERS->with(md5('query=test')), 'value2');
    Cache::put(CacheKey::LOAD_ORDER->with($this->loadOrder->slug), 'test-list-by-slug');
    Cache::put(CacheKey::LOAD_ORDER->with('other-slug'), 'should-remain');

    $this->loadOrder->name = 'Updated Load Order';
    $this->loadOrder->save();

    expect(Cache::tags([CacheKey::LOAD_ORDERS->value])->get('test-lists'))->toBeNull()
        ->and(Cache::tags([CacheKey::LOAD_ORDERS->value])->get(CacheKey::LOAD_ORDERS->with(md5('query=test'))))->toBeNull()
        ->and(Cache::get(CacheKey::LOAD_ORDER->with($this->loadOrder->slug)))->toBeNull()
        ->and(Cache::get(CacheKey::LOAD_ORDER->with('other-slug')))->toBe('should-remain');
});

it('clears cache when load order is deleted', function () {
    // Set up multiple cache entries with query strings
    Cache::tags([CacheKey::LOAD_ORDERS->value])->put('test-lists', 'value1');
    Cache::tags([CacheKey::LOAD_ORDERS->value])->put(CacheKey::LOAD_ORDERS->with(md5('query=test')), 'value2');
    Cache::tags([CacheKey::LOAD_ORDERS->value])->put(CacheKey::LOAD_ORDERS->with(md5('game=skyrim')), 'value3');
    Cache::put(CacheKey::LOAD_ORDER->with($this->loadOrder->slug), 'test-list-by-slug');
    Cache::put(CacheKey::LOAD_ORDER->with('other-slug'), 'should-remain');

    $this->loadOrder->delete();

    expect(Cache::tags([CacheKey::LOAD_ORDERS->value])->get('test-lists'))->toBeNull()
        ->and(Cache::tags([CacheKey::LOAD_ORDERS->value])->get(CacheKey::LOAD_ORDERS->with(md5('query=test'))))->toBeNull()
        ->and(Cache::tags([CacheKey::LOAD_ORDERS->value])->get(CacheKey::LOAD_ORDERS->with(md5('game=skyrim'))))->toBeNull()
        ->and(Cache::get(CacheKey::LOAD_ORDER->with($this->loadOrder->slug)))->toBeNull()
        ->and(Cache::get(CacheKey::LOAD_ORDER->with('other-slug')))->toBe('should-remain');
});
