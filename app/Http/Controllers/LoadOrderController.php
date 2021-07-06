<?php

namespace App\Http\Controllers;

use App\Helpers\CreateSlug;
use App\Http\Resources\LoadOrderCollection;
use Illuminate\Http\Request;
use App\Models\LoadOrder;
use App\Rules\ValidFilename;
use App\Rules\ValidMimeType;
use App\Rules\ValidNumLines;
use App\Rules\ValidSemver;
use App\Services\UploadService;
use App\Models\File;
use App\Models\Game;
use App\Models\User;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Database\Eloquent\Builder;

class LoadOrderController extends Controller
{

	public function index(Request $request)
	{
		$lists = QueryBuilder::for(LoadOrder::class)
			->allowedFilters([
				AllowedFilter::callback('author', function (Builder $query, $value) {	
					$query->whereUserId(User::whereName($value)->first()->id);
				}),
			])
			->allowedSorts([
				AllowedSort::field('created', 'created_at'),
				AllowedSort::field('updated', 'updated_at')
			])
			->where('is_private', false)
			->paginate(1)
			->appends(request()->query());
		
		return new LoadOrderCollection($lists);
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
