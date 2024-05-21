<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Resources\v1\GameResource;
use App\Models\Game;

class GameController extends ApiController
{
    public function index()
    {
        $games = Game::orderBy('name', 'asc')->withCount('loadOrders')->get();

        return GameResource::collection($games);
    }

    public function show(Game $game)
    {
        $game->load('loadOrders');

        return new GameResource($game);
    }
}
