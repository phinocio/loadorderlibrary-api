<?php

declare(strict_types=1);

namespace Tests\Unit\Model;

use App\Models\File;
use App\Models\LoadOrder;

test('to array', function () {
    $file = File::factory()->create()->refresh();

    $array = $file->toArray();

    expect($array)->toHaveKeys([
        'id',
        'name',
        'clean_name',
        'size_in_bytes',
        'created_at',
        'updated_at',
    ]);
});

test('lists relationship', function () {
    $file = File::factory()->create();
    $loadOrder = LoadOrder::factory()->create();

    $file->lists()->attach($loadOrder);

    expect($file->lists)->toHaveCount(1)
        ->and($file->lists->first())->toBeInstanceOf(LoadOrder::class);
});
