<?php

declare(strict_types=1);

namespace App\Actions\v1\LoadOrder;

use App\Filters\FiltersAuthorName;
use App\Filters\FiltersGameName;
use App\Models\LoadOrder;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

final class GetLoadOrders
{
    public function execute(Request $request, bool $includePrivate = false): mixed
    {
        $lists = QueryBuilder::for(LoadOrder::class)
            ->allowedFilters([
                AllowedFilter::custom('author', new FiltersAuthorName),
                AllowedFilter::custom('game', new FiltersGameName),
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                AllowedSort::field('created', 'created_at'),
                AllowedSort::field('updated', 'updated_at'),
            ])
            ->when(! $includePrivate, fn ($query) => $query->where('is_private', '=', false))
            ->when($request->query('query'), function ($query) use ($request) {
                return $query->where(function ($q) use ($request) {
                    $q->orWhere('name', 'LIKE', '%'.$request->query('query').'%')
                        ->orWhere('description', 'LIKE', '%'.$request->query('query').'%')
                        ->orWhereRelation('author', 'name', 'LIKE', '%'.$request->query('query').'%')
                        ->orWhereRelation('game', 'name', 'LIKE', '%'.$request->query('query').'%');
                });
            });

        $queryParams = $request->query();
        $pageParams = isset($queryParams['page']) && is_array($queryParams['page'])
            ? $queryParams['page']
            : [];

        if (isset($pageParams['size']) && $pageParams['size'] === 'all') {
            return $lists->clone()->get()->all();
        }

        if (isset($pageParams['size']) && is_numeric($pageParams['size'])) {
            return $lists->clone()->paginate((int) $pageParams['size']);
        }

        return $lists->clone()->paginate(30);
    }
}
