<?php

namespace App\Http\Controllers\Api\v1;

use App\Filters\FiltersAuthorName;
use App\Filters\FiltersGameName;
use App\Helpers\CreateSlug;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLoadOrderRequest;
use App\Http\Resources\v1\LoadOrderResource;
use App\Models\File;
use App\Models\LoadOrder;
use App\Services\UploadService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Storage;
use Throwable;

class LoadOrderController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(LoadOrder::class, 'loadOrder');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $lists = QueryBuilder::for(LoadOrder::class)
            ->allowedFilters([
                AllowedFilter::custom('author', new FiltersAuthorName()),
                AllowedFilter::custom('game', new FiltersGameName()),
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                AllowedSort::field('created', 'created_at'),
                AllowedSort::field('updated', 'updated_at'),
            ])
            ->where('is_private', false)
            ->paginate(120)
            ->appends(request()->query());

        return LoadOrderResource::collection($lists);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLoadOrderRequest $request): LoadOrderResource
    {
        $validated = $request->validated();
        $fileNames = UploadService::uploadFiles($validated['files']);

        // Determine the expiration of the list. Logged-in users default to
        // perm, guests default to 24h. If the expires field was not sent, check that.
        if (!array_key_exists('expires', $validated)) {
            auth()->check() ? $validated['expires'] = 'perm' : $validated['expires'] = '24h';
        }

        if (!array_key_exists('private', $validated)) {
            $validated['private'] = false;
        }

        $validated['expires'] = match ($validated['expires']) {
            '3h' => Carbon::now()->addHours(3),
            '3d' => Carbon::now()->addDays(3),
            '1w' => Carbon::now()->addWeek(),
            'perm' => null,
            default => auth()->check() ? null : Carbon::now()->addHours(24),
        };

        $loadOrder = new LoadOrder();

        // Since multiple DB actions need to be taken, use a transaction.
        DB::transaction(function () use ($loadOrder, $fileNames, $validated) {
            // Persist the file entries to the database.
            $fileIds = [];
            foreach ($fileNames as $file) {
                $file['clean_name'] = explode('-', $file['name'])[1];
                $file['size_in_bytes'] = Storage::disk('uploads')->size($file['name']);
                $fileIds[] = File::firstOrCreate($file)->id;
            }

            $loadOrder->user_id     = auth()->check() ? auth()->user()->id : null;
            $loadOrder->game_id     = (int) $validated['game'];
            $loadOrder->slug        = CreateSlug::new($validated['name']);
            $loadOrder->name        = $validated['name'];
            $loadOrder->description = $validated['description'] ?? null;
            $loadOrder->version     = $validated['version'] ?? null;
            // We simply remove the http/s of an input url, so we can add https:// to all on display.
            // If a site doesn't support TLS at this point, that's on them, I'm not linking to an insecure url.
            $loadOrder->website     = str_replace(['https://', 'http://'], '', $validated['website'] ?? null) ?: null;
            $loadOrder->discord     = str_replace(['https://', 'http://'], '', $validated['discord'] ?? null) ?: null;
            $loadOrder->readme      = str_replace(['https://', 'http://'], '', $validated['readme'] ?? null) ?: null;
            $loadOrder->is_private  = (bool) $validated['private'];
            $loadOrder->expires_at  = $validated['expires'];
            $loadOrder->save();
            $loadOrder->files()->attach($fileIds);
        });


        // return
        return new LoadOrderResource($loadOrder->load('author'));
    }

    /**
     * Display the specified resource.
     */
    public function show(LoadOrder $loadOrder)
    {
        return new LoadOrderResource($loadOrder->load('files'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LoadOrder $loadOrder)
    {
        try {
            $loadOrder->delete();
            return response()->json(null, 204);
        } catch (Throwable $th) {
            return response()->json(['message' => 'something went wrong deleting the load order. Please let Phinocio know.', 'error' => $th->getMessage()], 500);
        }
    }
}
