<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\File;

use App\Enums\v1\CacheKey;
use App\Http\Resources\v1\File\FileResource;
use App\Models\File;
use Illuminate\Support\Facades\Cache;

final class FileController
{
    public function show(string $name): FileResource
    {
        /** @var File $file */
        $file = Cache::rememberForever(
            CacheKey::FILE->with($name),
            fn () => File::query()->where('name', $name)->firstOrFail()
        );

        return new FileResource(
            $file,
        );
    }
}
