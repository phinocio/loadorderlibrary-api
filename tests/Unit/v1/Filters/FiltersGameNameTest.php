<?php

declare(strict_types=1);

namespace Tests\Unit\v1\Filters;

use App\Filters\FiltersGameName;
use App\Models\Game;
use App\Models\LoadOrder;

it('filters load orders by game name', function () {
    $filterGameName = new FiltersGameName;
    $targetGame = Game::factory()->create(['name' => 'Skyrim']);
    $otherGame = Game::factory()->create(['name' => 'Fallout']);

    LoadOrder::factory()->count(2)->for($targetGame, 'game')->create();
    LoadOrder::factory()->count(3)->for($otherGame, 'game')->create();

    $query = LoadOrder::query()->with('game');

    $filterGameName($query, 'Skyrim', 'game');

    $results = $query->get();
    expect($results)->toHaveCount(2)
        ->and($results->every(fn ($loadOrder) => $loadOrder->game->is($targetGame)))->toBeTrue();
});

it('returns empty collection when no games match the name', function () {
    $filterGameName = new FiltersGameName;
    $game = Game::factory()->create(['name' => 'Existing Game']);
    LoadOrder::factory()->count(3)->for($game, 'game')->create();

    $query = LoadOrder::query();

    $filterGameName($query, 'Non Existent Game', 'game');

    expect($query->get())->toHaveCount(0);
});

it('is case insensitive when matching game names', function () {
    $filterGameName = new FiltersGameName;
    $game = Game::factory()->create(['name' => 'Skyrim']);
    LoadOrder::factory()->count(2)->for($game, 'game')->create();

    $query = LoadOrder::query();

    $filterGameName($query, 'skyrim', 'game');

    expect($query->get())->toHaveCount(2);
});
