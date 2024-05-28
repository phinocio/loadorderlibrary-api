<?php

namespace App\Http\Controllers\Api\v1;

use App\Filters\FiltersAuthorName;
use App\Filters\FiltersGameName;
use App\Http\Requests\v1\StoreLoadOrderRequest;
use App\Http\Requests\v1\UpdateLoadOrderRequest;
use App\Http\Resources\v1\LoadOrderResource;
use App\Models\File;
use App\Models\LoadOrder;
use App\Policies\v1\LoadOrderPolicy;
use App\Services\UploadService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Throwable;

class LoadOrderController extends ApiController
{
    protected string $policyClass = LoadOrderPolicy::class;

    /**
     * Display a listing of the resource.
     *
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @noinspection DuplicatedCode
     */
    public function index(): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', LoadOrder::class);

        if (request('page') && request('page')['size'] === 'all') {
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
                ->where('is_private', '=', false)
                ->when(request('query'), function ($query) {
                    $query->where(function ($query) {
                        $query->orWhere('name', 'like', '%'.request('query').'%')
                            ->orWhere('description', 'like', '%'.request('query').'%')
                            ->orWhereRelation('author', 'name', 'like', '%'.request('query').'%')
                            ->orWhereRelation('game', 'name', 'like', '%'.request('query').'%');
                    });
                })->get();
        } else {
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
                ->where('is_private', '=', false)
                ->when(request('query'), function ($query) {
                    $query->where(function ($query) {
                        $query->orWhere('name', 'like', '%'.request('query').'%')
                            ->orWhere('description', 'like', '%'.request('query').'%')
                            ->orWhereRelation('author', 'name', 'like', '%'.request('query').'%')
                            ->orWhereRelation('game', 'name', 'like', '%'.request('query').'%');
                    });
                })->jsonPaginate(900, 30);
        }

        return LoadOrderResource::collection($lists);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLoadOrderRequest $request): LoadOrderResource|JsonResponse
    {
        Gate::authorize('create', LoadOrder::class);

        // TODO: Surely there's a better solution to allow guest uploads than this?
        // Return with a 401 (or maybe 422?) if there is no user associated with a token so a list is
        // not accidentally uploaded anonymously if the token was typo'd.
        if (request()->bearerToken() && $user = Auth::guard('sanctum')->user()) {
            Auth::setUser($user);
            if (! $user->tokenCan('create')) {
                return response()->json(
                    ['message' => "This action is forbidden. (Token doesn't have permission for this action.)"],
                    403
                );
            }
        } elseif (request()->bearerToken() && ! Auth::guard('sanctum')->check()) {
            return response()->json(['message' => 'Unauthenticated. (Make sure the token is correct.)'], 401);
        }

        $validated = $request->validated();
        $fileNames = UploadService::uploadFiles($validated['files']);

        // Determine the expiration of the list. Logged-in users default to
        // perm, guests default to 24h. If the expires field was not sent, check that.
        if (! array_key_exists('expires', $validated)) {
            auth()->check() ? $validated['expires'] = 'perm' : $validated['expires'] = '24h';
        }

        if (! array_key_exists('private', $validated)) {
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

            $loadOrder->user_id = auth()->check() ? auth()->user()->id : null;
            $loadOrder->game_id = (int) $validated['game'];
            //            $loadOrder->slug        = CreateSlug::new($validated['name']);
            $loadOrder->name = $validated['name'];
            $loadOrder->description = $validated['description'] ?? null;
            $loadOrder->version = $validated['version'] ?? null;
            // We simply remove the http/s of an input url, so we can add https:// to all on display.
            // If a site doesn't support TLS at this point, that's on them, I'm not linking to an insecure url.
            $loadOrder->website = str_replace(['https://', 'http://'], '', $validated['website'] ?? null) ?: null;
            $loadOrder->discord = str_replace(['https://', 'http://'], '', $validated['discord'] ?? null) ?: null;
            $loadOrder->readme = str_replace(['https://', 'http://'], '', $validated['readme'] ?? null) ?: null;
            $loadOrder->is_private = (bool) $validated['private'];
            $loadOrder->expires_at = $validated['expires'];
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
        Gate::authorize('view', $loadOrder);

        return new LoadOrderResource($loadOrder->load('files'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLoadOrderRequest $request, LoadOrder $loadOrder): LoadOrderResource
    {
        Gate::authorize('update', $loadOrder);

        // Unlike the store() method, auth is done on the route level

        $validated = $request->validated();

        $fileNames = [];
        if (isset($validated['files'])) {
            $fileNames = UploadService::uploadFiles($validated['files']);
        }

        // Determine the expiration of the list. Logged-in users default to
        // perm, guests default to 24h. If the expires field was not sent, check that.
        if (! array_key_exists('expires', $validated)) {
            auth()->check() ? $validated['expires'] = 'perm' : $validated['expires'] = '24h';
        }

        if (! array_key_exists('private', $validated)) {
            $validated['private'] = false;
        }

        $validated['expires'] = match ($validated['expires']) {
            '3h' => Carbon::now()->addHours(3),
            '3d' => Carbon::now()->addDays(3),
            '1w' => Carbon::now()->addWeek(),
            'perm' => null,
            default => auth()->check() ? null : Carbon::now()->addHours(24),
        };

        // Since multiple DB actions need to be taken, use a transaction.
        DB::transaction(function () use ($loadOrder, $fileNames, $validated) {
            $fileIds = [];
            if (count($fileNames) > 0) {
                // Persist the file entries to the database.
                foreach ($fileNames as $file) {
                    $file['clean_name'] = explode('-', $file['name'])[1];
                    $file['size_in_bytes'] = Storage::disk('uploads')->size($file['name']);
                    $fileIds[] = File::firstOrCreate($file)->id;
                }
            }

            $loadOrder->game_id = (int) $validated['game'];
            $loadOrder->name = $validated['name'];
            $loadOrder->description = $validated['description'] ?? null;
            $loadOrder->version = $validated['version'] ?? null;
            // We simply remove the http/s of an input url, so we can add https:// to all on display.
            // If a site doesn't support TLS at this point, that's on them, I'm not linking to an insecure url.
            $loadOrder->website = str_replace(['https://', 'http://'], '', $validated['website'] ?? null) ?: null;
            $loadOrder->discord = str_replace(['https://', 'http://'], '', $validated['discord'] ?? null) ?: null;
            $loadOrder->readme = str_replace(['https://', 'http://'], '', $validated['readme'] ?? null) ?: null;
            $loadOrder->is_private = (bool) $validated['private'];
            $loadOrder->expires_at = $validated['expires'];
            $loadOrder->save();

            if (count($fileIds) > 0) {
                $loadOrder->files()->sync($fileIds);
            }
        });

        // We load the game relation again to get the updated game,
        // otherwise it returns with the old game.
        return new LoadOrderResource($loadOrder->load(['author', 'game']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LoadOrder $loadOrder)
    {
        Gate::authorize('delete', $loadOrder);

        try {
            $loadOrder->delete();

            return response()->json(null, 204);
        } catch (Throwable $th) {
            return response()->json([
                'message' => 'something went wrong deleting the load order. Please let Phinocio know.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
