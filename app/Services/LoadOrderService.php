<?php

namespace App\Services;

use App\Enums\CacheTag;
use App\Filters\FiltersAuthorName;
use App\Filters\FiltersGameName;
use App\Helpers\CacheKey;
use App\Models\File;
use App\Models\LoadOrder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class LoadOrderService
{
    public function getLoadOrders(Request $request): Collection|LengthAwarePaginator
    {
        $cacheKey = CacheKey::create($request->getPathInfo(), $request->query());

        return Cache::tags([CacheTag::LOAD_ORDERS->value])->flexible($cacheKey, [600, 900], function () use ($request) {
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
                ->when($request->query('query'), function ($query) use ($request) {
                    $query->where(function ($query) use ($request) {
                        $query->orWhere('name', 'ILIKE', '%'.$request->query('query').'%')
                            ->orWhere('description', 'ILIKE', '%'.$request->query('query').'%')
                            ->orWhereRelation('author', 'name', 'ILIKE', '%'.$request->query('query').'%')
                            ->orWhereRelation('game', 'name', 'ILIKE', '%'.$request->query('query').'%');
                    });
                })
                ->with(['game', 'author']);

            if ($request->query('page') && isset($request->query('page')['size']) && $request->query('page')['size'] === 'all') {
                return $lists->clone()->get();
            } else {
                return $lists->clone()->jsonPaginate(900, 30);
            }
        });
    }

    /**
     * Get a single load order with its relationships
     */
    public function getLoadOrder(LoadOrder $loadOrder, Request $request): LoadOrder
    {
        $cacheKey = CacheKey::create($request->getPathInfo(), [], false);

        return Cache::tags([CacheTag::LOAD_ORDERS->value, CacheTag::LOAD_ORDER_ITEM->withSuffix($loadOrder->id)])->flexible($cacheKey, [600, 900], function () use ($loadOrder) {
            return $loadOrder->load(['game', 'author', 'files']);
        });
    }

    /**
     * Create a new load order
     */
    public function createLoadOrder(array $validated, array $fileNames): LoadOrder
    {
        $validated = $this->processValidatedData($validated);

        $loadOrder = new LoadOrder();

        DB::transaction(function () use ($loadOrder, $fileNames, $validated) {
            $fileIds = $this->processFiles($fileNames);

            $loadOrder->user_id = Auth::check() ? Auth::user()->id : null;
            $loadOrder->game_id = (int) $validated['game'];
            $this->setLoadOrderAttributes($loadOrder, $validated);
            $loadOrder->save();
            $loadOrder->files()->attach($fileIds);
        });

        return $loadOrder->load(['game', 'author']);
    }

    /**
     * Update an existing load order
     */
    public function updateLoadOrder(LoadOrder $loadOrder, array $validated, array $fileNames, Request $request): LoadOrder
    {
        $isAuthed = Auth::check();
        $validated = $this->processValidatedData($validated, $isAuthed);

        DB::transaction(function () use ($loadOrder, $fileNames, $validated) {
            $fileIds = [];
            if (count($fileNames) > 0) {
                $fileIds = $this->processFiles($fileNames);
            }

            $this->setLoadOrderAttributes($loadOrder, $validated);
            $loadOrder->save();

            if (count($fileIds) > 0) {
                $loadOrder->files()->sync($fileIds);
            }
        });

        $this->clearLoadOrderCache($request);

        return $loadOrder->load(['author', 'game']);
    }

    /**
     * Delete a load order
     */
    public function deleteLoadOrder(LoadOrder $loadOrder): bool
    {
        return $loadOrder->delete();
    }

    private function processValidatedData(array $validated): array
    {
        $isAuthed = Auth::check();

        // Set default expiration
        if (! array_key_exists('expires', $validated)) {
            $isAuthed ? $validated['expires'] = 'perm' : $validated['expires'] = '24h';
        }

        // Set default privacy
        if (! array_key_exists('private', $validated)) {
            $validated['private'] = false;
        }

        // Calculate expiration date
        $validated['expires'] = match ($validated['expires']) {
            '3h' => Carbon::now()->addHours(3),
            '3d' => Carbon::now()->addDays(3),
            '1w' => Carbon::now()->addWeek(),
            'perm' => null,
            default => $isAuthed ? null : Carbon::now()->addHours(24),
        };

        return $validated;
    }

    private function processFiles(array $fileNames): array
    {
        $fileIds = [];
        foreach ($fileNames as $file) {
            $file['clean_name'] = explode('-', $file['name'])[1];
            $file['size_in_bytes'] = Storage::disk('uploads')->size($file['name']);
            $fileIds[] = File::query()->firstOrCreate($file)->id;
        }
        return $fileIds;
    }

    private function setLoadOrderAttributes(LoadOrder $loadOrder, array $validated): void
    {
        $loadOrder->game_id = (int) $validated['game'];
        $loadOrder->name = $validated['name'];
        $loadOrder->description = $validated['description'] ?? null;
        $loadOrder->version = $validated['version'] ?? null;

        // Clean URLs by removing http/https prefixes
        $loadOrder->website = str_replace(['https://', 'http://'], '', $validated['website'] ?? null) ?: null;
        $loadOrder->discord = str_replace(['https://', 'http://'], '', $validated['discord'] ?? null) ?: null;
        $loadOrder->readme = str_replace(['https://', 'http://'], '', $validated['readme'] ?? null) ?: null;

        $loadOrder->is_private = (bool) $validated['private'];
        $loadOrder->expires_at = $validated['expires'];
    }

    public function clearLoadOrderCache(Request $request): void
    {
        Cache::forget(Str::replace('/', '-', $request->getPathInfo()));
    }
}
