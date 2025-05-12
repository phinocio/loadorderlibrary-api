<?php

declare(strict_types=1);

namespace Tests\Unit\v1\Actions\LoadOrder;

use App\Actions\v1\LoadOrder\GetLoadOrders;
use App\Models\Game;
use App\Models\LoadOrder;
use App\Models\User;
use Illuminate\Http\Request;

beforeEach(function () {
    $this->action = new GetLoadOrders;
});

test('it returns paginated load orders by default', function () {
    // Create 35 load orders (more than default page size of 30)
    LoadOrder::factory()->count(35)->create(['is_private' => false]);

    $request = Request::create('/api/v1/load-orders', 'GET');
    $result = $this->action->execute($request);

    expect($result)->toBeObject()
        ->and($result->perPage())->toBe(30)
        ->and($result->total())->toBe(35)
        ->and($result->items())->toHaveCount(30);
});

test('it respects custom page size', function () {
    LoadOrder::factory()->count(10)->create(['is_private' => false]);

    $request = Request::create('/api/v1/load-orders?page[size]=5', 'GET');
    $result = $this->action->execute($request);

    expect($result)->toBeObject()
        ->and($result->perPage())->toBe(5)
        ->and($result->items())->toHaveCount(5);
});

test('it returns all records when page size is "all"', function () {
    LoadOrder::factory()->count(35)->create(['is_private' => false]);

    $request = Request::create('/api/v1/load-orders?page[size]=all', 'GET');
    $result = $this->action->execute($request);

    expect($result)->toBeArray()
        ->toHaveCount(35);
});

test('it filters by author name', function () {
    $author = User::factory()->create(['name' => 'John Doe']);
    LoadOrder::factory()->count(3)
        ->for($author, 'author')
        ->create(['is_private' => false]);

    $request = Request::create('/api/v1/load-orders?filter[author]=John', 'GET');
    $result = $this->action->execute($request);

    expect($result)->toBeObject()
        ->and($result->total())->toBe(3);
});

test('it filters by game name', function () {
    $game = Game::factory()->create(['name' => 'Skyrim']);
    LoadOrder::factory()->count(3)
        ->for($game)
        ->create(['is_private' => false]);

    $request = Request::create('/api/v1/load-orders?filter[game]=Skyrim', 'GET');
    $result = $this->action->execute($request);

    expect($result)->toBeObject()
        ->and($result->total())->toBe(3);
});

test('it searches across multiple fields', function () {
    $author = User::factory()->create(['name' => 'SearchAuthor']);
    $game = Game::factory()->create(['name' => 'SearchGame']);

    // Create load order with matching name
    LoadOrder::factory()->create([
        'name' => 'SearchTest Load Order',
        'description' => 'Something else',
        'is_private' => false,
    ]);

    // Create load order with matching description
    LoadOrder::factory()->create([
        'name' => 'Something else',
        'description' => 'SearchTest description',
        'is_private' => false,
    ]);

    // Create load order with matching author
    LoadOrder::factory()
        ->for($author, 'author')
        ->create(['is_private' => false]);

    // Create load order with matching game
    LoadOrder::factory()
        ->for($game)
        ->create(['is_private' => false]);

    // Create an unrelated load order
    LoadOrder::factory()->create(['is_private' => false]);

    $request = Request::create('/api/v1/load-orders?query=SearchTest', 'GET');
    $result = $this->action->execute($request);

    expect($result)->toBeObject()
        ->and($result->total())->toBe(2); // Should find the ones with matching name and description
});

test('it excludes private load orders', function () {
    LoadOrder::factory()->count(3)->create(['is_private' => true]);
    LoadOrder::factory()->count(2)->create(['is_private' => false]);

    $request = Request::create('/api/v1/load-orders', 'GET');
    $result = $this->action->execute($request);

    expect($result)->toBeObject()
        ->and($result->total())->toBe(2);
});

test('it sorts by creation date in descending order by default', function () {
    LoadOrder::factory()->create([
        'is_private' => false,
        'created_at' => now()->subDays(2),
    ]);
    LoadOrder::factory()->create([
        'is_private' => false,
        'created_at' => now()->subDays(1),
    ]);
    LoadOrder::factory()->create([
        'is_private' => false,
        'created_at' => now(),
    ]);

    $request = Request::create('/api/v1/load-orders', 'GET');
    $result = $this->action->execute($request);

    expect($result)->toBeObject()
        ->and($result->items()[0]['created_at'])->toBeGreaterThan($result->items()[1]['created_at'])
        ->and($result->items()[1]['created_at'])->toBeGreaterThan($result->items()[2]['created_at']);
});

test('it allows sorting by update date', function () {
    LoadOrder::factory()->create([
        'is_private' => false,
        'updated_at' => now()->subDays(2),
    ]);
    LoadOrder::factory()->create([
        'is_private' => false,
        'updated_at' => now(),
    ]);

    $request = Request::create('/api/v1/load-orders?sort=updated', 'GET');
    $result = $this->action->execute($request);

    expect($result)->toBeObject()
        ->and($result->items()[0]['updated_at'])->toBeLessThan($result->items()[1]['updated_at']);
});
