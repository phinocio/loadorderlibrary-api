<?php

declare(strict_types=1);

namespace App\Actions\v1\File;

use App\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

final class UploadFile
{
    public function execute(UploadedFile $file): File
    {
        try {
            $contents = file_get_contents($file->path());
            if ($contents === false) {
                throw new RuntimeException('Failed to read file contents');
            }
        } catch (Throwable $e) {
            throw new RuntimeException('Failed to read file contents', 0, $e);
        }

        $contents = preg_replace('/[\r\n]+/', "\n", $contents);
        if ($contents === null) {
            throw new RuntimeException('Failed to normalize line endings');
        }

        file_put_contents($file->path(), $contents);
        $fileName = mb_strtolower(md5($file->getClientOriginalName().$contents).'-'.$file->getClientOriginalName());

        if (! Storage::disk('uploads')->exists($fileName)) {
            Storage::disk('uploads')->putFileAs('', $file, $fileName);
        }

        return File::firstOrCreate(
            ['name' => $fileName],
            [
                'clean_name' => $file->getClientOriginalName(),
                'size_in_bytes' => $file->getSize(),
            ]
        );
    }
}
