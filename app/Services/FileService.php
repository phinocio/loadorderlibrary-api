<?php

namespace App\Services;

use App\Enums\CacheTag;
use App\Models\LoadOrder;
use App\Models\File;
use App\Helpers\CacheKey;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use STS\ZipStream\Builder;
use STS\ZipStream\Facades\Zip;

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

    public function downloadAllFiles(LoadOrder $loadOrder): Builder|null
    {
        $listFiles = [];

        foreach ($loadOrder->files as $file) {
            $listFiles[] = [
                'name' => $file->name,
                'clean_name' => $file->clean_name
            ];
        }

        if (empty($listFiles)) {
            return null;
        }

        $zip = Zip::create($loadOrder->name.'.zip');
        foreach ($listFiles as $file) {
            $tmpFile = Storage::disk('uploads')->temporaryUrl($file['name'], now()->addMinutes(self::TEMP_URL_EXPIRATION));
            $zip->add($tmpFile, $file['clean_name']);
        }

        return $zip;
    }

    public function downloadFile(LoadOrder $loadOrder, string $fileName, Request $request): RedirectResponse|null
    {
        $file = $this->getFile($loadOrder, $fileName, $request);

        if (!$file) {
            return null;
        }

        $url = Storage::disk('uploads')->temporaryUrl($file->name, now()->addMinutes(self::TEMP_URL_EXPIRATION), [
            'ResponseContentType' => 'application/octet-stream',
            'ResponseContentDisposition' => 'attachment; filename="'.$file->clean_name.'"'
        ]);
        return redirect($url);
    }
}
