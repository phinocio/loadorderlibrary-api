<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\File;

use App\Actions\v1\File\DeleteFile;
use App\Actions\v1\File\GetFileContent;
use App\Enums\v1\CacheKey;
use App\Http\Resources\v1\File\FileResource;
use App\Models\File;
use Illuminate\Support\Facades\Cache;

final class FileController
{
    public function show(string $name, GetFileContent $getFileContent): FileResource
    {
        /** @var File $file */
        $file = Cache::rememberForever(
            CacheKey::FILE->with($name),
            fn () => File::where('name', $name)->firstOrFail()
        );

        /** @var array<int, string> $content */
        $content = Cache::rememberForever(
            CacheKey::FILE->with($name, 'content'),
            fn () => $getFileContent->execute($file)
        );

        return new FileResource(
            $file,
            $content
        );
    }

    public function destroy(File $file, DeleteFile $deleteFile): void
    {
        $deleteFile->execute($file);
    }
}
