<?php

namespace App\Observers;

use App\Enums\CacheTag;
use App\Models\LoadOrder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LoadOrderObserver
{
    /**
     * Handle the LoadOrder "created" event.
     */
    public function created(LoadOrder $loadOrder): void
    {
        $this->clearCache($loadOrder);
    }

    /**
     * Handle the LoadOrder "updated" event.
     */
    public function updated(LoadOrder $loadOrder): void
    {
        $this->clearCache($loadOrder);
    }

    /**
     * Handle the LoadOrder "deleted" event.
     */
    public function deleted(LoadOrder $loadOrder): void
    {
        $this->clearCache($loadOrder);
    }

    /**
     * Handle the LoadOrder "restored" event.
     */
    public function restored(LoadOrder $loadOrder): void
    {
        $this->clearCache($loadOrder);
    }

    /**
     * Handle the LoadOrder "force deleted" event.
     */
    public function forceDeleted(LoadOrder $loadOrder): void
    {
        $this->clearCache($loadOrder);
    }

    /**
     * Clear cache for the load order
     */
    private function clearCache(LoadOrder $loadOrder): void
    {
        try {
            // Clear load order collection cache
            Cache::tags([CacheTag::LOAD_ORDERS->value])->flush();

            // Clear specific load order cache
            Cache::tags([CacheTag::LOAD_ORDER_ITEM->withSuffix($loadOrder->id)])->flush();

            // Clear game caches
            Cache::tags([CacheTag::GAMES->value])->flush();
            Cache::tags([CacheTag::GAME_ITEM->withSuffix($loadOrder->game_id)])->flush();

            // Clear files cache
            Cache::tags([CacheTag::FILES->value])->flush();
            foreach ($loadOrder->files as $file) {
                Cache::tags([CacheTag::FILES->withSuffix($loadOrder->id . '-' . $file->clean_name)])->flush();
            }

            // Clear stats cache
            Cache::tags([CacheTag::STATS->value])->flush();


            Log::info('Cache cleared for load order: ' . $loadOrder->id);
        } catch (\Exception $e) {
            Log::error('Failed to clear cache with tags: ' . $e->getMessage());
        }
    }
}
