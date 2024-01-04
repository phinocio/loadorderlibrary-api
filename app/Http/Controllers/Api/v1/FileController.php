<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\LoadOrder;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class FileController extends Controller
{
    public function index(LoadOrder $loadOrder)
    {
        $listFiles = [];

        foreach ($loadOrder->files as $file) {
            $listFiles[] = strtolower($file->name);
        }

        $zip = new ZipArchive();
        $zipFile = $loadOrder->name . '.zip';
        if ($zip->open(storage_path('app/tmp/' . $zipFile), ZipArchive::CREATE)) {
            foreach ($listFiles as $file) {
                $zip->addFile(storage_path('app/uploads/' . $file), preg_replace('/[a-zA-Z0-9_]*-/i', '', $file));
            }
            $zip->close();

            return \Storage::download('tmp/' . $zipFile);
        }
    }


    public function show(LoadOrder $loadOrder, string $fileName): StreamedResponse
    {
        echo $loadOrder->name . ' - ' . $fileName;
        $listFiles = [];

        foreach ($loadOrder->files as $file) {
            $listFiles[] = strtolower($file->name);
        }

        $file = implode(preg_grep("/$fileName/", $listFiles));

        return Storage::download('uploads/' . $file, $fileName);
    }
}
