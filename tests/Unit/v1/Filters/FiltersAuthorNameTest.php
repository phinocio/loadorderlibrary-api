<?php

declare(strict_types=1);

namespace Tests\Unit\v1\Filters;

use App\Filters\FiltersAuthorName;
use App\Models\LoadOrder;
use App\Models\User;

it('filters load orders by author name', function () {
    $filterAuthorName = new FiltersAuthorName;
    $targetAuthor = User::factory()->create(['name' => 'John Doe']);
    $otherAuthor = User::factory()->create(['name' => 'Jane Smith']);

    LoadOrder::factory()->count(2)->for($targetAuthor, 'author')->create();
    LoadOrder::factory()->count(3)->for($otherAuthor, 'author')->create();

    $query = LoadOrder::query()->with('author');

    $filterAuthorName($query, 'John Doe', 'author');

    $results = $query->get();
    expect($results)->toHaveCount(2)
        ->and($results->every(fn ($loadOrder) => $loadOrder->author->is($targetAuthor)))->toBeTrue();
});

it('returns empty collection when no authors match the name', function () {
    $filterAuthorName = new FiltersAuthorName;
    $author = User::factory()->create(['name' => 'Existing Author']);
    LoadOrder::factory()->count(3)->for($author, 'author')->create();

    $query = LoadOrder::query();

    $filterAuthorName($query, 'Non Existent Author', 'author');

    expect($query->get())->toHaveCount(0);
});

it('is case sensitive when matching author names', function () {
    $filterAuthorName = new FiltersAuthorName;
    $author = User::factory()->create(['name' => 'John Doe']);
    LoadOrder::factory()->count(2)->for($author, 'author')->create();

    $query = LoadOrder::query();

    $filterAuthorName($query, 'john doe', 'author');

    expect($query->get())->toHaveCount(0);
});
