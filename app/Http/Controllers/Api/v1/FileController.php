<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Resources\v1\FileResource;
use App\Models\LoadOrder;
use App\Services\FileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\StreamedResponse;

// TODO: The index/show methods should be the ones that return just the model representation (name, clean_name, etc)
// and the actual downloading of the files should be moved to either a dedicated DownloadController, or different
// methods in this file.

class FileController extends ApiController
{
    protected FileService $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * Get all files for a load order
     */
    public function index(LoadOrder $loadOrder): AnonymousResourceCollection
    {
        $files = $this->fileService->getFiles($loadOrder, request());
        return FileResource::collection(collect($files));
    }

    /**
     * Get a specific file by name
     */
    public function show(LoadOrder $loadOrder, string $fileName): FileResource|JsonResponse
    {
        $file = $this->fileService->getFile($loadOrder, $fileName, request());

        if (!$file) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return new FileResource($file);
    }

    /**
     * Download all files as a zip archive
     */
    public function downloadAll(LoadOrder $loadOrder): StreamedResponse|JsonResponse
    {
        $response = $this->fileService->downloadAllFiles($loadOrder);

        if (!$response) {
            return response()->json(['message' => 'No files to download'], 404);
        }

        return $response;
    }

    /**
     * Download a specific file
     */
    public function download(LoadOrder $loadOrder, string $fileName): StreamedResponse|JsonResponse
    {
        $response = $this->fileService->downloadFile($loadOrder, $fileName, request());

        if (!$response) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return $response;
    }

    /**
     * Embed a file (used for displaying file content in the UI)
     */
    public function embed(LoadOrder $loadOrder, string $fileName): FileResource|JsonResponse
    {
        $file = $this->fileService->getFile($loadOrder, $fileName, request());

        if (!$file) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return new FileResource($file);
    }
}
