<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\File;

use App\Actions\v1\File\DownloadAllFiles;
use App\Actions\v1\File\DownloadFile;
use App\Enums\v1\CacheKey;
use App\Models\File;
use App\Models\LoadOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use STS\ZipStream\Builder;

final class FileDownloadController
{
    public function download(string $name, DownloadFile $downloadFile): RedirectResponse
    {
        /** @var File $file */
        $file = Cache::rememberForever(
            CacheKey::FILE->with($name),
            fn (): File => File::query()->where('name', $name)->firstOrFail()
        );

        // Check if file exists in storage
        if (! Storage::disk('uploads')->exists($file->name)) {
            abort(404);
        }

        return $downloadFile->execute($file);
    }

    public function downloadAllFiles(LoadOrder $loadOrder, DownloadAllFiles $downloadAllFiles): Builder
    {
        // Ensure the files relationship is loaded
        $loadOrder->load('files');

        if (! count($loadOrder->files)) {
            abort(404);
        }

        return $downloadAllFiles->execute($loadOrder);
    }
}
