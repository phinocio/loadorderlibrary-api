<?php

declare(strict_types=1);

use App\Models\File;
use App\Models\Game;
use App\Models\LoadOrder;
use App\Models\User;

test('to array', function () {
    $loadOrder = LoadOrder::factory()->create()->refresh();

    $array = $loadOrder->toArray();

    expect($array)->toHaveKeys([
        'id',
        'name',
        'slug',
        'description',
        'version',
        'website',
        'discord',
        'readme',
        'is_private',
        'expires_at',
        'user_id',
        'game_id',
        'created_at',
        'updated_at',
    ]);
});

test('author relationship', function () {
    $user = User::factory()->create();
    $loadOrder = LoadOrder::factory()->create([
        'user_id' => $user->id,
    ]);

    $author = $loadOrder->author;

    expect($author)->toBeInstanceOf(User::class)
        ->and($author->id)->toBe($user->id);
});

test('game relationship', function () {
    $game = Game::factory()->create();
    $loadOrder = LoadOrder::factory()->create([
        'game_id' => $game->id,
    ]);

    $relatedGame = $loadOrder->game;

    expect($relatedGame)->toBeInstanceOf(Game::class)
        ->and($relatedGame->id)->toBe($game->id);
});

test('files relationship returns associated files', function () {
    $loadOrder = LoadOrder::factory()->create();
    $file = File::factory()->create();
    $loadOrder->files()->attach($file->id);

    $files = $loadOrder->files;

    expect($files)->toHaveCount(1)
        ->and($files->first())->toBeInstanceOf(File::class)
        ->and($files->first()->id)->toBe($file->id);
});

test('expired scope returns expired load orders', function () {
    // Create an expired load order (date in the past)
    $expiredLoadOrder = LoadOrder::factory()->create([
        'expires_at' => now()->subDays(1),
    ]);

    // Create a non-expired load order (date in the future)
    LoadOrder::factory()->create([
        'expires_at' => now()->addDays(1),
    ]);

    $expiredLoadOrders = LoadOrder::query()->expired()->get();

    expect($expiredLoadOrders)->toHaveCount(1)
        ->and($expiredLoadOrders->first()->id)->toBe($expiredLoadOrder->id);
});

test('sluggable generates slug from name', function () {
    $loadOrder = LoadOrder::factory()->create([
        'name' => 'Test Load Order Name',
    ]);

    expect($loadOrder->slug)->toBe('test-load-order-name');
});
