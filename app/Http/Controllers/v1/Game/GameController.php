<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Game;

use App\Enums\v1\CacheKey;
use App\Http\Resources\v1\Game\GameResource;
use App\Models\Game;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

final class GameController
{
    public function index(): AnonymousResourceCollection
    {
        $games = Cache::rememberForever(
            CacheKey::GAMES->value,
            fn () => Game::query()->withCount(['lists' => fn (Builder $query) => $query->where('is_private', false)])->orderBy('name')->get()
        );

        return GameResource::collection($games);
    }

    public function show(string $game): GameResource
    {
        $game = Cache::rememberForever(
            CacheKey::GAME->with($game),
            fn () => Game::query()->withCount(['lists' => fn (Builder $query) => $query->where('is_private', false)])->where('slug', $game)->orWhere('name', $game)->firstOrFail()
        );

        return new GameResource($game);
    }
}
