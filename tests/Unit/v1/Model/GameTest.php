<?php

declare(strict_types=1);

use App\Models\Game;
use App\Models\LoadOrder;

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

test('lists relationship returns associated load orders', function () {
    $game = Game::factory()->create();
    LoadOrder::factory()->create([
        'game_id' => $game->id,
    ]);

    $list = $game->lists()->first();

    expect($game->lists)->toHaveCount(1)
        ->and($list)->toBeInstanceOf(LoadOrder::class);
});
