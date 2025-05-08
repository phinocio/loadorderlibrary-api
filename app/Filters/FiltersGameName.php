<?php

declare(strict_types=1);

namespace App\Filters;

use App\Models\LoadOrder;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

/** @implements Filter<LoadOrder> */
final class FiltersGameName implements Filter
{
    public function __invoke(Builder $query, mixed $value, string $property): void
    {
        $query->whereHas('game', function (Builder $query) use ($value) {
            $query->where('name', $value);
        });
    }
}
