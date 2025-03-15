<?php

namespace App\Services;

use App\Enums\CacheTag;
use App\Helpers\CacheKey;
use App\Models\Game;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GameService
{
    /**
     * Get all games with caching
     */
    public function getAllGames(Request $request): Collection
    {
        $cacheKey = CacheKey::create($request->getPathInfo(), $request->query());

        try {
            return Cache::tags([CacheTag::GAMES->value])->flexible($cacheKey, [3600, 7200], function () {
                return Game::orderBy('name', 'asc')->withCount('loadOrders')->get();
            });
        } catch (\Exception $e) {
            Log::error('Cache error in getAllGames: ' . $e->getMessage());

            // Fallback if cache tags are not supported
            return Game::orderBy('name', 'asc')->withCount('loadOrders')->get();
        }
    }

    /**
     * Get a specific game with caching
     */
    public function getGame(Game $game, Request $request): Game
    {
        $cacheKey = CacheKey::create($request->getPathInfo(), [], false);

        try {
            return Cache::tags([
                CacheTag::GAMES->value,
                CacheTag::GAME_ITEM->withSuffix($game->id)
                ])->flexible($cacheKey, [3600, 7200], function () use ($game) {
                    return $game->load('loadOrders');
                });
        } catch (\Exception $e) {
            Log::error('Cache error in getGame: ' . $e->getMessage());

            // Fallback if cache tags are not supported
            return $game->load('loadOrders');
        }
    }
}
