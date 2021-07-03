<?php

namespace App\Http\Controllers;

use App\Helpers\CreateSlug;
use Illuminate\Http\Request;
use App\Models\LoadOrder;
use App\Rules\ValidFilename;
use App\Rules\ValidMimeType;
use App\Rules\ValidNumLines;
use App\Rules\ValidSemver;
use App\Services\UploadService;
use App\Models\File;

class LoadOrderController extends Controller
{

	public function index(Request $request)
	{	
		$query = LoadOrder::whereIsPrivate(false);

		if($request->query('author')) {
			$author = User::whereName($request->query('author'))->first();
			$query->whereUserId($author->id);
		}
		$lists = LoadOrder::where('is_private', false)->get();
		return response()->json($lists, 200);
	}

	public function store(Request $request)
	{
		// validate
		$validated = request()->validate([
			'name' => 'required',
			'game_id' => 'required|int',
			'version' => ['string', 'nullable', new ValidSemver, 'max:15'],	
			'is_private' => 'required|boolean',
			'user_id' => 'int|nullable',
			'description' => 'string|nullable',
		]);

		$validatedFiles = request()->validate([
			'files' => 'required',
			'files.*' => [new ValidMimeType, 'max:128', new ValidNumLines, new ValidFilename],
		]);

		$validatedFiles['files'] = UploadService::uploadFiles($validatedFiles['files']);

		$fileIds = [];
		foreach ($validatedFiles['files'] as $file) {
			$file['clean_name'] = explode('-', $file['name'])[1];
			$file['size_in_bytes'] = \Storage::disk('uploads')->size($file['name']);
			$fileIds[] = File::firstOrCreate($file)->id;
		}
		
		$validated['slug'] = CreateSlug::new($validated['name']);
		
		// persist
		$list = LoadOrder::create($validated);
		$list->files()->attach($fileIds);

		// return
		return response()->json($list);
	}

	public function destroy(LoadOrder $loadOrder)
	{
		$loadOrder->delete();
	}
}
