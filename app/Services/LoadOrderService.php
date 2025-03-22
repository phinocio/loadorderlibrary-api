<?php

namespace App\Services;

use App\Filters\FiltersAuthorName;
use App\Filters\FiltersGameName;
use App\Models\File;
use App\Models\LoadOrder;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class LoadOrderService
{
    public function getLoadOrders(Request $request): LengthAwarePaginator|array
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

        if (isset($request->query('page')['size']) && $request->query('page')['size'] === 'all') {
            return $lists->clone()->get()->all();
        } else {
            return $lists->clone()->jsonPaginate(900, 30);
        }
    }

    /**
     * Get a single load order with its relationships
     */
    public function getLoadOrder(LoadOrder $loadOrder): LoadOrder
    {
        return $loadOrder->load(['game', 'author', 'files']);
    }

    /**
     * Create a new load order
     */
    public function createLoadOrder(array $data, array $files): LoadOrder
    {
        $loadOrder = new LoadOrder();
        $loadOrder->game_id = (int) $data['game'];
        $loadOrder->user_id = Auth::id();
        $loadOrder->name = $data['name'];
        $loadOrder->description = $data['description'] ?? null;
        $loadOrder->version = $data['version'] ?? null;
        $loadOrder->website = $this->cleanUrl($data['website'] ?? null);
        $loadOrder->discord = $this->cleanUrl($data['discord'] ?? null);
        $loadOrder->readme = $this->cleanUrl($data['readme'] ?? null);
        $loadOrder->is_private = $data['private'] ?? false;
        $loadOrder->expires_at = $this->calculateExpiration($data['expires'] ?? null);

        DB::transaction(function () use ($loadOrder, $files) {
            $loadOrder->save();

            if (!empty($files)) {
                $fileIds = array_map(function ($file) {
                    return File::firstOrCreate([
                        'name' => $file['name'],
                        'clean_name' => explode('-', $file['name'])[1],
                        'size_in_bytes' => Storage::disk('uploads')->size($file['name'])
                    ])->id;
                }, $files);

                $loadOrder->files()->attach($fileIds);
            }
        });

        return $loadOrder->load(['game', 'author']);
    }

    /**
     * Update an existing load order
     */
    public function updateLoadOrder(LoadOrder $loadOrder, array $data, array $files): LoadOrder
    {
        DB::transaction(function () use ($loadOrder, $data, $files) {
            $loadOrder->game_id = (int) $data['game'];
            $loadOrder->name = $data['name'];
            $loadOrder->description = $data['description'] ?? null;
            $loadOrder->version = $data['version'] ?? null;
            $loadOrder->website = $this->cleanUrl($data['website'] ?? null);
            $loadOrder->discord = $this->cleanUrl($data['discord'] ?? null);
            $loadOrder->readme = $this->cleanUrl($data['readme'] ?? null);
            $loadOrder->is_private = $data['private'] ?? false;
            $loadOrder->expires_at = $this->calculateExpiration($data['expires'] ?? null);

            $loadOrder->touch(); // Force update the updated_at timestamp
            $loadOrder->save();

            if (!empty($files)) {
                $fileIds = array_map(function ($file) {
                    return File::firstOrCreate([
                        'name' => $file['name'],
                        'clean_name' => explode('-', $file['name'])[1],
                        'size_in_bytes' => Storage::disk('uploads')->size($file['name'])
                    ])->id;
                }, $files);

                $loadOrder->files()->sync($fileIds);
            }
        });

        return $loadOrder->load(['author', 'game']);
    }

    /**
     * Delete a load order
     */
    public function deleteLoadOrder(LoadOrder $loadOrder): bool
    {
        return $loadOrder->delete();
    }

    private function cleanUrl(?string $url): ?string
    {
        if (!$url) {
            return null;
        }
        return str_replace(['https://', 'http://'], '', $url) ?: null;
    }

    private function calculateexpiration(?string $expires): ?Carbon
    {
        if (!$expires) {
            return Auth::check() ? null : Carbon::now()->addHours(24);
        }

        return match ($expires) {
            '3h' => Carbon::now()->addHours(3),
            '3d' => Carbon::now()->addDays(3),
            '1w' => Carbon::now()->addWeek(),
            'perm' => null,
            default => Auth::check() ? null : Carbon::now()->addHours(24),
        };
    }
}
