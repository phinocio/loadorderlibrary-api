<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\File;

use App\Actions\v1\File\DownloadAllFiles;
use App\Actions\v1\File\DownloadFile;
use App\Enums\v1\CacheKey;
use App\Models\File;
use App\Models\LoadOrder;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use STS\ZipStream\Builder;

final class FileDownloadController
{
    public function download(string $name, DownloadFile $downloadFile): Response
    {
        /** @var File $file */
        $file = Cache::rememberForever(
            CacheKey::FILE->with($name),
            fn (): File => File::query()->where('name', $name)->firstOrFail()
        );

        if (! $file->exists) {
            abort(404);
        }

        return $downloadFile->execute($file);
    }

    public function downloadAllFiles(LoadOrder $loadOrder, DownloadAllFiles $downloadAllFiles): ?Builder
    {
        return $downloadAllFiles->execute($loadOrder);
    }
}
