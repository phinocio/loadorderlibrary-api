<?php

namespace App\Observers;

use App\Enums\CacheTag;
use App\Models\Game;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GameObserver
{
    /**
     * Handle the Game "created" event.
     */
    public function created(Game $game): void
    {
        $this->clearCache($game);
    }

    /**
     * Handle the Game "updated" event.
     */
    public function updated(Game $game): void
    {
        $this->clearCache($game);
    }

    /**
     * Handle the Game "deleted" event.
     */
    public function deleted(Game $game): void
    {
        $this->clearCache($game);
    }

    /**
     * Handle the Game "restored" event.
     */
    public function restored(Game $game): void
    {
        $this->clearCache($game);
    }

    /**
     * Handle the Game "force deleted" event.
     */
    public function forceDeleted(Game $game): void
    {
        $this->clearCache($game);
    }

    /**
     * Clear cache for the game and related load orders
     */
    private function clearCache(Game $game): void
    {
        try {
            // Clear game collection cache
            Cache::tags([CacheTag::GAMES->value])->flush();

            // Clear specific game cache
            Cache::tags([CacheTag::GAME_ITEM->withSuffix($game->id)])->flush();

            // Clear load order caches since they're related to games
            Cache::tags([CacheTag::LOAD_ORDERS->value])->flush();

            // Clear stats cache
            Cache::tags([CacheTag::STATS->value])->flush();

            // Clear files cache
            Cache::tags([CacheTag::FILES->value])->flush();

            Log::info('Cache cleared for game: ' . $game->id);
        } catch (\Exception $e) {
            Log::error('Failed to clear cache with tags: ' . $e->getMessage());
            $this->clearCacheWithoutTags();
        }
    }

    /**
     * Fallback method to clear cache without tags
     * This is used when the cache driver doesn't support tags
     */
    private function clearCacheWithoutTags(): void
    {
        try {
            // Clear general caches
            Cache::forget('stats');

            // Clear route caches
            $routes = [
                '/api/v1/lists',
                '/api/v1/games',
                '/api/v1/stats',
            ];

            foreach ($routes as $route) {
                Cache::forget($route);
            }

            Log::info('Cache cleared without tags');
        } catch (\Exception $e) {
            Log::error('Failed to clear cache without tags: ' . $e->getMessage());
        }
    }
}
