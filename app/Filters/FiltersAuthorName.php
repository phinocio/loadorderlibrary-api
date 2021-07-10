<?php

namespace App\Filters;

use App\Models\User;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FiltersAuthorName implements Filter{
	public function __invoke(Builder $query, $value, string $property)
	{
		$query->whereHas('author', function (Builder $query) use ($value) {
			$query->whereUserId(User::whereName($value)->firstOrFail()->id);
		});
	}
}