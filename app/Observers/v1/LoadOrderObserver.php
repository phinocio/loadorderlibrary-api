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
        Cache::forget(CacheKey::LOAD_ORDERS->value);
    }

    /** Handle the LoadOrder "updated" event. */
    public function updated(LoadOrder $loadOrder): void
    {
        Cache::forget(CacheKey::LOAD_ORDERS->value);
        Cache::forget(CacheKey::LOAD_ORDER->with($loadOrder->slug));
    }

    /** Handle the LoadOrder "deleted" event. */
    public function deleted(LoadOrder $loadOrder): void
    {
        Cache::forget(CacheKey::LOAD_ORDERS->value);
        Cache::forget(CacheKey::LOAD_ORDER->with($loadOrder->slug));
    }

    /** Handle the LoadOrder "restored" event. */
    public function restored(LoadOrder $loadOrder): void
    {
        //
    }

    /** Handle the LoadOrder "force deleted" event. */
    public function forceDeleted(LoadOrder $loadOrder): void
    {
        //
    }
}
