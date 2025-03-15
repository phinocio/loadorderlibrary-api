<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Resources\v1\GameResource;
use App\Models\Game;
use App\Services\GameService;

class GameController extends ApiController
{
    protected GameService $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    public function index()
    {
        $games = $this->gameService->getAllGames(request());

        return GameResource::collection($games);
    }

    public function show(Game $game)
    {
        $game = $this->gameService->getGame($game, request());

        return new GameResource($game);
    }
}
