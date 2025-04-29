<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Admin;

use App\Actions\v1\Game\CreateGame;
use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\Admin\AdminCreateGameRequest;
use App\Http\Resources\v1\Game\GameResource;

final class AdminGameController extends ApiController
{
    public function store(AdminCreateGameRequest $request, CreateGame $createGame): GameResource
    {
        /** @var array{name: string} $data */
        $data = $request->validated();
        $game = $createGame->execute($data);

        return new GameResource($game);
    }
}
