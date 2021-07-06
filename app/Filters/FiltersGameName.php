<?php

namespace App\Filters;

use App\Models\Game;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FiltersGameName implements Filter{
	public function __invoke(Builder $query, $value, string $property)
	{
		$query->whereHas('game', function (Builder $query) use ($value) {
			$author = Game::whereName($value)->first();

			if($author) {
				$query->whereGameId($author->id);
			} else {
				abort(422, 'Game name not found');
			}
		});
	}
}