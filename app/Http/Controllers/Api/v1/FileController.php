<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\FileResource;
use App\Models\LoadOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

// TODO: The index/show methods should be the ones that return just the model representation (name, clean_name, etc)
// and the actual downloading of the files should be moved to either a dedicated DownloadController, or different
// methods in this file.

class FileController extends Controller
{
    public function index(LoadOrder $loadOrder)
    {
        $listFiles = [];

        foreach ($loadOrder->files as $file) {
            $listFiles[] = strtolower($file->name);
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
    }

    public function show(LoadOrder $loadOrder, string $fileName): StreamedResponse
    {
        $file = $loadOrder->load('files')->files()->whereCleanName($fileName)->first();

        return Storage::download('uploads/'.$file->name, $fileName);
    }

    public function embed(LoadOrder $loadOrder, string $fileName): FileResource|JsonResponse
    {
        $file = $loadOrder->load('files')->files()->whereCleanName($fileName)->first();

        if (! $file) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return new FileResource($loadOrder->load('files')->files()->whereCleanName($fileName)->first());
    }
}
