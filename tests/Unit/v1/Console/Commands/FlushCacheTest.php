<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Cache;

it('flushes all cache successfully', function () {
    // Set up some cache data
    Cache::put('test-key-1', 'test-value-1');
    Cache::put('test-key-2', 'test-value-2');
    Cache::tags(['test-tag'])->put('tagged-key', 'tagged-value');

    // Verify cache is set
    expect(Cache::has('test-key-1'))->toBeTrue()
        ->and(Cache::has('test-key-2'))->toBeTrue()
        ->and(Cache::tags(['test-tag'])->has('tagged-key'))->toBeTrue();

    // Execute the command
    $this->artisan('cache:flush-all')
        ->expectsOutput('All cache has been flushed successfully.')
        ->assertSuccessful();

    // Verify all cache has been cleared
    expect(Cache::has('test-key-1'))->toBeFalse()
        ->and(Cache::has('test-key-2'))->toBeFalse()
        ->and(Cache::tags(['test-tag'])->has('tagged-key'))->toBeFalse();
});

it('handles empty cache gracefully', function () {
    // Ensure cache is empty
    Cache::flush();

    // Execute the command on empty cache
    $this->artisan('cache:flush-all')
        ->expectsOutput('All cache has been flushed successfully.')
        ->assertSuccessful();
});
