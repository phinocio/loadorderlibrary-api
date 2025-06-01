<?php

declare(strict_types=1);

namespace App\Actions\v1\File;

use App\Models\LoadOrder;
use Illuminate\Support\Facades\Storage;
use STS\ZipStream\Builder;
use STS\ZipStream\Facades\Zip;

final class DownloadAllFiles
{
    private const TEMP_URL_EXPIRATION = 5;

    /** Create a zip file containing all files from a load order. */
    public function execute(LoadOrder $loadOrder): Builder
    {
        $listFiles = [];

        foreach ($loadOrder->files as $file) {
            $listFiles[] = [
                'name' => $file->name,
                'clean_name' => $file->clean_name,
            ];
        }

        $zip = Zip::create($loadOrder->name.'.zip');
        foreach ($listFiles as $file) {
            $tmpFile = Storage::disk('uploads')->temporaryUrl($file['name'], now()->addMinutes(self::TEMP_URL_EXPIRATION));
            $zip->add($tmpFile, $file['clean_name']);
        }

        return $zip;
    }
}
