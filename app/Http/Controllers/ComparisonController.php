<?php

namespace App\Http\Controllers;

use App\Models\LoadOrder;
use App\Services\ComparisonService;
use Illuminate\Http\Request;

class ComparisonController extends Controller
{
    public function show(string $list1, string $list2)
	{
		// use CompareService to check if lists have files that differ
		// contruct the response with a resource collection
		$results = ComparisonService::compare($list1, $list2);

		return $results;
	}
}
