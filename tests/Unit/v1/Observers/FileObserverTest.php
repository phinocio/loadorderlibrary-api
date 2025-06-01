<?php

declare(strict_types=1);

namespace Tests\Unit\Observers;

use App\Enums\v1\CacheKey;
use App\Models\File;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    $this->file = File::factory()->create([
        'name' => 'test-file.txt',
        'clean_name' => 'test.txt',
        'size_in_bytes' => 100,
    ]);
});

it('clears cache when file is created', function () {
    Cache::put(CacheKey::FILES->value, 'test-files');

    File::factory()->create([
        'name' => 'another-file.txt',
    ]);

    expect(Cache::has(CacheKey::FILES->value))->toBeFalse();
});

it('clears cache when file is deleted', function () {
    Cache::put(CacheKey::FILES->value, 'test-files');
    Cache::put(CacheKey::FILE->with($this->file->name), 'test-file-by-name');

    $this->file->delete();

    expect(Cache::has(CacheKey::FILES->value))->toBeFalse()
        ->and(Cache::has(CacheKey::FILE->with($this->file->name)))->toBeFalse();
});
