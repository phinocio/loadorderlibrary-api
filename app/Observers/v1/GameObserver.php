<?php

declare(strict_types=1);

namespace App\Observers\v1;

use App\Enums\v1\CacheKey;
use App\Models\Game;
use Cache;

final class GameObserver
{
    /** Handle the Game "created" event. */
    public function created(Game $game): void
    {
        Cache::forget(CacheKey::GAMES->value);
    }

    /** Handle the Game "updated" event. */
    public function updated(Game $game): void
    {
        Cache::forget(CacheKey::GAMES->value);
        Cache::forget(CacheKey::GAME->with($game->slug));
        Cache::forget(CacheKey::GAME->with($game->name));
    }

    /** Handle the Game "deleted" event. */
    public function deleted(Game $game): void
    {
        Cache::forget(CacheKey::GAMES->value);
        Cache::forget(CacheKey::GAME->with($game->slug));
        Cache::forget(CacheKey::GAME->with($game->name));
    }
}
