<?php

declare(strict_types=1);

namespace App\Actions\v1\File;

use App\Models\File;
use Illuminate\Support\Facades\Storage;

final class DeleteFile
{
    public function execute(File $file): void
    {
        if (Storage::disk('uploads')->exists($file->name)) {
            Storage::disk('uploads')->delete($file->name);
        }

        $file->delete();
    }
}
