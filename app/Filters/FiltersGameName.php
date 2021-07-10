<?php

namespace App\Filters;

use App\Models\Game;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FiltersGameName implements Filter{
	public function __invoke(Builder $query, $value, string $property)
	{
		$query->whereHas('game', function (Builder $query) use ($value) {
			$query->whereGameId(Game::whereName($value)->firstOrFail()->id);
		});
	}
}