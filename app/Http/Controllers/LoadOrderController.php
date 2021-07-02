<?php

namespace App\Http\Controllers;

use App\Helpers\CreateSlug;
use Illuminate\Http\Request;
use App\Models\LoadOrder;
use App\Rules\ValidFilename;
use App\Services\UploadService;

class LoadOrderController extends Controller
{

	public function index()
	{
		$lists = LoadOrder::where('is_private', false)->get();
		return response()->json($lists, 200);
	}

	public function store(Request $request)
	{
		// validate
		$validated = request()->validate([
			'name' => 'required',
			'game_id' => 'required|int',
			'files' => 'required',
			'files.*' => ['mimetypes:text/plain,application/x-wine-extension-ini', 'max:128', new ValidFilename],
			'is_private' => 'required|boolean',
			'user_id' => 'int|nullable',
			'description' => 'string|nullable',
		]);
		
		$validated['slug'] = CreateSlug::new($validated['name']);
		$validated['files'] = UploadService::uploadFiles($validated['files']);
		
		// persist
		$list = LoadOrder::create($validated);

		// return
		return response()->json($list);
	}

	public function destroy(LoadOrder $loadOrder)
	{
		$loadOrder->delete();
	}
}
