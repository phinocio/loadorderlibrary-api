<?php

declare(strict_types=1);

use App\Models\Game;

test('to array', function () {
    $game = Game::factory()->create()->refresh();

    $array = $game->toArray();

    expect($array)->toHaveKeys([
        'id',
        'name',
        'slug',
        'created_at',
        'updated_at',
    ]);
});
