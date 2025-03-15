<?php

namespace App\Services;

use App\Enums\CacheTag;
use App\Models\LoadOrder;
use App\Models\File;
use App\Helpers\CacheKey;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class FileService
{
    // Default expiration time for temporary URLs (5 minutes)
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

    public function downloadAllFiles(LoadOrder $loadOrder): StreamedResponse|null
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
        if ($zip->open(storage_path('app/tmp/'.$zipFile), ZipArchive::CREATE)) {
            foreach ($listFiles as $file) {
                $zip->addFile(storage_path('app/uploads/'.$file), preg_replace('/[a-zA-Z0-9_]*-/i', '', $file));
            }
            $zip->close();

            return Storage::download('tmp/'.$zipFile);
        }

        return null;
    }

    public function downloadFile(LoadOrder $loadOrder, string $fileName, Request $request): StreamedResponse|null
    {
        $file = $this->getFile($loadOrder, $fileName, $request);

        if (!$file) {
            return null;
        }

        return Storage::download('uploads/'.$file->name, $fileName);
    }
}
