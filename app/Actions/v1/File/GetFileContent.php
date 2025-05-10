<?php

declare(strict_types=1);

namespace App\Actions\v1\File;

use App\Models\File;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

final class GetFileContent
{
    /**
     * Retrieves the content of a file.
     *
     * @param  File  $file  The file to retrieve content from.
     * @return array<int, string> The content of the file as an array of lines.
     *
     * @throws RuntimeException If the file cannot be read.
     */
    public function execute(File $file): array
    {
        $content = Storage::disk('uploads')->get($file->name);

        if (! $content) {
            throw new RuntimeException('Failed to read file contents');
        }

        return explode("\n", $content);
    }
}
