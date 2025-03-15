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
        Cache::tags([CacheTag::GAME_ITEM->withSuffix($game->id)])->flush();
        Cache::tags([CacheTag::STATS->value])->flush();
    }


    /**
     * Handle the Game "deleted" event.
     */
    public function deleted(Game $game): void
    {
        Cache::tags([CacheTag::GAMES->value])->flush();
        Cache::tags([CacheTag::GAME_ITEM->withSuffix($game->id)])->flush();
        Cache::tags([CacheTag::LOAD_ORDERS->value])->flush();
        foreach ($game->loadOrders as $loadOrder) {
            Cache::tags([CacheTag::LOAD_ORDER_ITEM->withSuffix($loadOrder->id)])->flush();
        }
        Cache::tags([CacheTag::FILES->value])->flush();
        Cache::tags([CacheTag::STATS->value])->flush();
    }
}
