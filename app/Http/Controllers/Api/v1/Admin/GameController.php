<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\GameResource;
use App\Models\Game;
use Illuminate\Http\Request;
use Throwable;

class GameController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate(['name' => 'required|max:32']);

        $game = Game::create(['name' => $validated['name']]);

        return new GameResource($game);
    }

    public function update(Request $request, Game $game)
    {
        //
    }

    public function destroy(Game $game)
    {
        try {
            $game->delete();

            return response()->json(null, 204);
        } catch (Throwable $th) {
            return response()->json([
                'message' => 'something went wrong deleting the game.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
