<?php

namespace App\Services;

use App\Enums\CacheTag;
use App\Models\LoadOrder;
use App\Models\File;
use App\Helpers\CacheKey;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class FileService
{
    // Default expiration time for temporary URLs (15 minutes)
    private const TEMP_URL_EXPIRATION = 5;

    public function getFiles(LoadOrder $loadOrder, Request $request): Collection
    {
        $cacheKey = CacheKey::create($request->getPathInfo(), $request->query());

        try {
            return Cache::tags([CacheTag::FILES->value])->flexible($cacheKey, [3600, 7200], function () use ($loadOrder) {
                return $loadOrder->load('files')->files;
            });
        } catch (\Exception $e) {
            Log::error('Cache error in getFiles: ' . $e->getMessage());

            return $loadOrder->load('files')->files;
        }
    }

    public function getFile(LoadOrder $loadOrder, string $fileName, Request $request): File|null
    {
        $cacheKey = CacheKey::create($request->getPathInfo(), $request->query());

        try {
            return Cache::tags([CacheTag::FILES->withSuffix($loadOrder->id . '-' . $fileName)])->flexible($cacheKey, [3600, 7200], function () use ($loadOrder, $fileName) {
                return $loadOrder->files()->where('clean_name', $fileName)->first();
            });
        } catch (\Exception $e) {
            Log::error('Cache error in getFile: ' . $e->getMessage());

            return $loadOrder->files()->where('clean_name', $fileName)->first();
        }
    }

    public function downloadAllFiles(LoadOrder $loadOrder): RedirectResponse|StreamedResponse|null
    {
        $listFiles = [];

        foreach ($loadOrder->files as $file) {
            $listFiles[] = strtolower($file->name);
        }

        if (empty($listFiles)) {
            return null;
        }

        $zip = new ZipArchive();
        $zipFile = $loadOrder->name.'.zip';
        $tmpPath = storage_path('app/tmp/'.$zipFile);

        if ($zip->open($tmpPath, ZipArchive::CREATE)) {
            foreach ($listFiles as $file) {
                try {
                    // Get the file contents using a temporary URL
                    $tempUrl = Storage::disk('uploads')->temporaryUrl(
                        $file,
                        Carbon::now()->addMinutes(self::TEMP_URL_EXPIRATION)
                    );

                    // Get the contents from the temporary URL
                    $contents = file_get_contents($tempUrl);
                    if ($contents === false) {
                        Log::error('Failed to get contents from temporary URL for file: ' . $file);
                        continue;
                    }

                    $zip->addFromString(preg_replace('/[a-zA-Z0-9_]*-/i', '', $file), $contents);
                } catch (\Exception $e) {
                    Log::error('Failed to add file to zip: ' . $e->getMessage());
                    continue;
                }
            }
            $zip->close();

            // Create a temporary URL for the zip file
            return Storage::disk('tmp')->download($zipFile);
        }

        return null;
    }

    public function downloadFile(LoadOrder $loadOrder, string $fileName, Request $request): RedirectResponse|null
    {
        $file = $this->getFile($loadOrder, $fileName, $request);

        if (!$file) {
            return null;
        }

        try {
            $tempUrl = Storage::disk('uploads')->temporaryUrl(
                $file->name,
                Carbon::now()->addMinutes(self::TEMP_URL_EXPIRATION)
            );

            return redirect()->away($tempUrl);
        } catch (\Exception $e) {
            Log::error('Failed to generate temporary URL for file: ' . $e->getMessage());
            return null;
        }
    }
}
