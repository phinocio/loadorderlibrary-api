<?php

namespace App\Services;

use App\Models\LoadOrder;

class ComparisonService {

	public static function compare(string $list1, string $list2): array
	{
		$list1 = LoadOrder::whereSlug($list1)->first();
		$list2 = LoadOrder::whereSlug($list2)->first();

		$response = ComparisonService::compareLists($list1, $list2);
		return $response;
	}
	
	private static function compareLists($list1, $list2): array
	{
		// 1. Create a key:value pair of files in each list where key is file name and value is hash of the file.
		$list1Files = [];
		foreach ($list1->files as $list1File) {
			$filesParts = explode('-', $list1File->name);
			$list1Files[$filesParts[1]] = $filesParts[0];
		}

		$list2Files = [];
		foreach ($list2->files as $list2File) {
			$filesParts = explode('-', $list2File->name);
			$list2Files[$filesParts[1]] = $filesParts[0];
		}

		// 2. figure out the files list1 has missing and added compared to list2
		$missing = array_diff_key($list1Files, $list2Files);
		$added = array_diff_key($list2Files, $list1Files);

		// 3. get the list1 and 2 files arrays to only contain the files that they both have
		$list1Files = array_diff($list1Files, $missing);
		$list2Files = array_diff($list2Files, $added);

		// 4. isolate the files that both lists contain, but have differences bashed on the hash value (array_diff takes care of this for key:pair arrays)
		$differences = array_diff($list1Files, $list2Files);


		$finalDiffs = [];

		foreach (array_keys($differences) as $file) {
			$finalDiffs[$file] = [
				"list1" => array_map('trim', explode("\n", trim(str_replace('*', '', \Storage::get('uploads/' . $list1Files[$file] . '-' . $file))))),
				"list2" => array_map('trim', explode("\n", trim(str_replace('*', '', \Storage::get('uploads/' . $list2Files[$file] . '-' . $file)))))
			];
		}
		$results = ComparisonService::formatResponse($list1->slug, $list2->slug, array_keys($missing), array_keys($added), $finalDiffs);

		return $results;
	}

	private static function formatResponse($list1, $list2, $missing, $added, $differences)
	{
		$response = [
			"data" => [
				"differences" => $differences,
				"missing" => $missing,
				"added" => $added
			],
			"links" => [
				"url" => config('app.main') . "/compare/$list1/$list2",
				"self" => config('app.url') . "/compare/$list1/$list2"
			],
			"meta" => [
				"list1" => $list1,
				"list2" => $list2,
				"differences" => count($differences),
				"missing" => count($missing),
				"added" => count($added),
			]
		];

		return $response;
	}
}