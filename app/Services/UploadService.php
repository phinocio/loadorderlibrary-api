<?php

namespace App\Services;

class UploadService
{
    /**
     * Upload the files and return a list of the names for them.
     */
    public static function uploadFiles(array $files): array
    {
        // Get names for the files and return them
        return UploadService::getFileNames($files);
    }

    /**
     * Get a list of filenames with MD5 Hashes prepended, and store to disk if not already.
     */
    private static function getFileNames(array $files): array
    {
        $fileNames = [];

        foreach ($files as $file) {
            $contents = file_get_contents($file);
            $contents = preg_replace('/[\r\n]+/', "\n", $contents);
            file_put_contents($file, $contents);
            $fileName = strtolower(md5($file->getClientOriginalName().$contents).'-'.$file->getClientOriginalName());
            $fileNames[] = ['name' => $fileName];

            // Check if file exists, if not, save it to disk.
            if (! UploadService::checkFileExists($fileName)) {
                \Storage::putFileAs('uploads', $file, $fileName);
            }
        }

        return $fileNames;
    }

    /**
     * Check if a file already exists on disk.
     */
    private static function checkFileExists(string $fileName): bool
    {
        return in_array('uploads/'.$fileName, \Storage::files('uploads'));
    }
}
