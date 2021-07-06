<?php

namespace App\Filters;

use App\Models\User;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FiltersAuthorName implements Filter{
	public function __invoke(Builder $query, $value, string $property)
	{
		$query->whereHas('author', function (Builder $query) use ($value) {
			$author = User::whereName($value)->first();

			if($author) {
				$query->whereUserId($author->id);
			} else {
				abort(422, 'Author name not found');
			}
		});
	}
}