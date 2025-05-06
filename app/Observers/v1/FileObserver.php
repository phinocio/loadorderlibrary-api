<?php

declare(strict_types=1);

namespace App\Observers\v1;

use App\Enums\v1\CacheKey;
use App\Models\File;
use Illuminate\Support\Facades\Cache;

final class FileObserver
{
    public function created(File $file): void
    {
        Cache::forget(CacheKey::FILES->value);
    }

    public function deleted(File $file): void
    {
        Cache::forget(CacheKey::FILES->value);
        Cache::forget(CacheKey::FILE->with($file->name));
    }
}
