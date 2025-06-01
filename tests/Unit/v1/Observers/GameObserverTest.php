<?php

declare(strict_types=1);

namespace Tests\Unit\Observers;

use App\Enums\v1\CacheKey;
use App\Models\Game;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    $this->game = Game::factory()->create([
        'name' => 'Test Game',
        'slug' => 'test-game',
    ]);
});

it('clears cache when game is created', function () {
    // Set up cache values
    Cache::put(CacheKey::GAMES->value, 'test-games');

    // Create a new game (this will trigger the observer)
    Game::factory()->create([
        'name' => 'Another Game',
    ]);

    // Assert cache was cleared
    expect(Cache::has(CacheKey::GAMES->value))->toBeFalse();
});

it('clears cache when game is updated', function () {
    // Set up cache values
    Cache::put(CacheKey::GAMES->value, 'test-games');
    Cache::put(CacheKey::GAME->with($this->game->slug), 'test-game-by-slug');
    Cache::put(CacheKey::GAME->with($this->game->name), 'test-game-by-name');

    // Update the game
    $this->game->name = 'Updated Game Name';
    $this->game->save();

    // Assert cache was cleared
    expect(Cache::has(CacheKey::GAMES->value))->toBeFalse()
        ->and(Cache::has(CacheKey::GAME->with($this->game->slug)))->toBeFalse()
        ->and(Cache::has(CacheKey::GAME->with($this->game->name)))->toBeFalse();
});

it('clears cache when game is deleted', function () {
    // Set up cache values
    Cache::put(CacheKey::GAMES->value, 'test-games');
    Cache::put(CacheKey::GAME->with($this->game->slug), 'test-game-by-slug');
    Cache::put(CacheKey::GAME->with($this->game->name), 'test-game-by-name');

    // Delete the game
    $this->game->delete();

    // Assert cache was cleared
    expect(Cache::has(CacheKey::GAMES->value))->toBeFalse()
        ->and(Cache::has(CacheKey::GAME->with($this->game->slug)))->toBeFalse()
        ->and(Cache::has(CacheKey::GAME->with($this->game->name)))->toBeFalse();
});
