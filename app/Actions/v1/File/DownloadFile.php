<?php

declare(strict_types=1);

namespace App\Actions\v1\File;

use App\Models\File;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

final class DownloadFile
{
    /**
     * Stream the file download through Laravel to handle CORS properly.
     *
     * @throws RuntimeException
     * @throws BindingResolutionException
     */
    public function execute(File $file): Response
    {
        if (! Storage::disk('uploads')->exists($file->name)) {
            abort(404, 'File not found');
        }

        $fileContents = Storage::disk('uploads')->get($file->name);
        if ($fileContents === null) {
            abort(404, 'File contents not found');
        }

        $mimeType = Storage::disk('uploads')->mimeType($file->name) ?: 'application/octet-stream';

        return response($fileContents, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'attachment; filename="'.$file->clean_name.'"',
            'Content-Length' => mb_strlen($fileContents),
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
