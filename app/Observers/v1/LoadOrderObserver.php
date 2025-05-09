<?php

declare(strict_types=1);

namespace App\Observers\v1;

use App\Enums\v1\CacheKey;
use App\Models\LoadOrder;
use Illuminate\Support\Facades\Cache;

final class LoadOrderObserver
{
    /** Handle the LoadOrder "created" event. */
    public function created(LoadOrder $loadOrder): void
    {
        // Only need to flush the index since this is a new item
        Cache::tags([CacheKey::LOAD_ORDERS->value])->flush();
    }

    /** Handle the LoadOrder "updated" event. */
    public function updated(LoadOrder $loadOrder): void
    {
        // Flush both the index and the specific load order
        Cache::tags([CacheKey::LOAD_ORDERS->value])->flush();
        Cache::forget(CacheKey::LOAD_ORDER->with($loadOrder->slug));
    }

    /** Handle the LoadOrder "deleted" event. */
    public function deleted(LoadOrder $loadOrder): void
    {
        // Flush both the index and the specific load order
        Cache::tags([CacheKey::LOAD_ORDERS->value])->flush();
        Cache::forget(CacheKey::LOAD_ORDER->with($loadOrder->slug));
    }
}
