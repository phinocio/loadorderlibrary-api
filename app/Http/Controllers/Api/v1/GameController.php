<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\GameResource;
use App\Models\Game;
use Illuminate\Http\Request;
use Throwable;

class GameController extends Controller
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
