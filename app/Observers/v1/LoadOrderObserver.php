<?php

namespace App\Observers\v1;

use App\Models\LoadOrder;

class LoadOrderObserver
{
    /**
     * Handle the LoadOrder "created" event.
     */
    public function created(LoadOrder $loadOrder): void
    {
        //
    }

    /**
     * Handle the LoadOrder "updated" event.
     */
    public function updated(LoadOrder $loadOrder): void
    {
        //
    }

    /**
     * Handle the LoadOrder "deleted" event.
     */
    public function deleted(LoadOrder $loadOrder): void
    {
        //
    }

    /**
     * Handle the LoadOrder "restored" event.
     */
    public function restored(LoadOrder $loadOrder): void
    {
        //
    }

    /**
     * Handle the LoadOrder "force deleted" event.
     */
    public function forceDeleted(LoadOrder $loadOrder): void
    {
        //
    }
}
