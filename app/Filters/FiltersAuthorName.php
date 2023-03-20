<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class FiltersAuthorName implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $query->whereHas('author', function (Builder $query) use ($value) {
            $query->where('name', $value);
        });
    }
}
