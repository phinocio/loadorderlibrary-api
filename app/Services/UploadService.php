<?php

namespace App\Services;

class UploadService {

	/**
	 * Upload the files and return a list of the names for them.
	 *
	 * @return void
	 */
	public static function uploadFiles(array $files): array
	{
		// Get names for the files and return them
		return UploadService::getFileNames($files);
	}

	/**
	 * Get a list of filenames with MD5 Hashes prepended, and store to disk if not already.
	 *
	 * @param array $files
	 * @return array
	 */
	private static function getFileNames(array $files): array
	{
		$fileNames = [];

		foreach ($files as $file) {
			$contents = file_get_contents($file);
			$contents = preg_replace('/[\r\n]+/', "\n", $contents);
			file_put_contents($file, $contents);
			$fileName = md5($file->getClientOriginalName() . $contents) . '-' . $file->getClientOriginalName();
			array_push($fileNames, ['name' => $fileName]);

			// Check if file exists, if not, save it to disk.
			if (!UploadService::checkFileExists($fileName)) {
				\Storage::putFileAs('uploads', $file, $fileName);
			}
		}

		return $fileNames;
	}

	/**
	 * Check if a file already exists on disk.
	 *
	 * @param string $fileName
	 * @return boolean
	 */
	private static function checkFileExists(string $fileName): bool
	{
		return in_array('uploads/' . $fileName, \Storage::files('uploads'));
	}
}