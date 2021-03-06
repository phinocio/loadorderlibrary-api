<?php

namespace App\Http\Controllers;

use App\Filters\FiltersAuthorName;
use App\Filters\FiltersGameName;
use App\Helpers\CreateSlug;
use App\Http\Resources\LoadOrderCollection;
use App\Http\Resources\LoadOrderResource;
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
use Carbon\Carbon;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Database\Eloquent\Builder;

class LoadOrderController extends Controller
{

	public function index()
	{
		$lists = QueryBuilder::for(LoadOrder::class)
			->allowedFilters([
				AllowedFilter::custom('author', new FiltersAuthorName),
				AllowedFilter::custom('game', new FiltersGameName),
			])
			->defaultSort('-created_at')
			->allowedSorts([
				AllowedSort::field('created', 'created_at'),
				AllowedSort::field('updated', 'updated_at')
			])
			->where('is_private', false)
			->paginate(14)
			->appends(request()->query());
		
		return new LoadOrderCollection($lists);
	}

	public function store(Request $request)
	{
		// validate
		$validated = request()->validate([
			'name' => 'required|max:100',
			'game_id' => 'required|int',
			'version' => ['string', 'nullable', new ValidSemver, 'max:15'],	
			'is_private' => 'boolean',
			'user_id' => 'int|nullable',
			'expires_at' => 'string|nullable',
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

		// Temp code for auto deleting anonymous lists until I remake it better for the API.

		if (isset($validated['expires_at'])) {
			switch ($validated['expires_at']) {
				case '3h':
					$validated['expires_at'] = Carbon::now()->addHours(3);
					break;
				case '24h':
					$validated['expires_at'] = Carbon::now()->addHours(24);
					break;
				case '3d':
					$validated['expires_at'] = Carbon::now()->addDays(3);
					break;
				case '1w':
					$validated['expires_at'] = Carbon::now()->addWeek();
					break;
				case 'perm':
					$validated['expires_at'] = null;
					break;
				default:
					$validated['expires_at'] = Carbon::now()->addHours(24); // every list is anonymous atm.
					break;
			}
		}
		
		// persist
		$list = LoadOrder::create($validated);
		$list->files()->attach($fileIds);

		// return
		return new LoadOrderResource($list);
	}

	public function show(LoadOrder $loadOrder)
	{
		return new LoadOrderResource($loadOrder);
	}

	public function destroy(LoadOrder $loadOrder)
	{
		return response()->json(['Message' => 'Not implemented'], 501);
	}
}
