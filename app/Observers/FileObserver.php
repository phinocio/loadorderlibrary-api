<?php

namespace App\Observers;

use App\Enums\CacheTag;
use App\Models\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FileObserver
{
    public function created(File $file): void
    {
        $this->clearCache($file);
    }

    public function updated(File $file): void
    {
        $this->clearCache($file);
    }

    public function deleted(File $file): void
    {
        $this->clearCache($file);
    }


    private function clearCache(File $file): void
    {
        try {
            Cache::tags([CacheTag::FILES->value])->flush();
            Cache::tags([CacheTag::FILE_ITEM->withSuffix($file->id)])->flush();

            foreach ($file->loadOrders as $loadOrder) {
                Cache::tags([CacheTag::LOAD_ORDER_ITEM->withSuffix($loadOrder->id)])->flush();
            }

            Cache::tags([CacheTag::STATS->value])->flush();

            Log::info('Cache cleared for file: ' . $file->id);
        } catch (\Exception $e) {
            Log::error('Failed to clear cache with tags: ' . $e->getMessage());
        }
    }
}
