<?php

declare(strict_types=1);

namespace App\Actions\v1\Game;

use App\Models\Game;

final class CreateGame
{
    /** @param array{name: string} $data */
    public function execute(array $data): Game
    {
        return Game::create([
            'name' => $data['name'],
        ]);
    }
}
