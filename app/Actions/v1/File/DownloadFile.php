<?php

declare(strict_types=1);

namespace App\Actions\v1\File;

use App\Models\File;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

final class DownloadFile
{
    private const TEMP_URL_EXPIRATION = 5;

    /**
     * The number of minutes the temporary URL will be valid for.
     *
     *
     * @throws RuntimeException
     * @throws BindingResolutionException
     */
    public function execute(File $file): RedirectResponse
    {
        $url = Storage::disk('uploads')->temporaryUrl($file->name, now()->addMinutes(self::TEMP_URL_EXPIRATION), [
            'ResponseContentType' => 'application/octet-stream',
            'ResponseContentDisposition' => 'attachment; filename="'.$file->clean_name.'"',
        ]);

        return redirect($url);
    }
}
