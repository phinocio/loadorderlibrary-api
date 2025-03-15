<?php

namespace App\Observers;

use App\Enums\CacheTag;
use App\Models\Game;
use Illuminate\Support\Facades\Cache;

class GameObserver
{
    /**
     * Handle the Game "created" event.
     */
    public function created(Game $game): void
    {
        Cache::tags([CacheTag::GAMES->value])->flush();
    }

    /**
     * Handle the Game "updated" event.
     */
    public function updated(Game $game): void
    {
        Cache::tags([CacheTag::GAMES->value])->flush();
    }

    /**
     * Handle the Game "deleted" event.
     */
    public function deleted(Game $game): void
    {
        Cache::tags([CacheTag::GAMES->value])->flush();
    }

    /**
     * Handle the Game "restored" event.
     */
    public function restored(Game $game): void
    {
        Cache::tags([CacheTag::GAMES->value])->flush();
    }

    /**
     * Handle the Game "force deleted" event.
     */
    public function forceDeleted(Game $game): void
    {
        Cache::tags([CacheTag::GAMES->value])->flush();
    }
}
