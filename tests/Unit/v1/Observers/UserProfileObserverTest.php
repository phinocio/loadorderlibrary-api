<?php

declare(strict_types=1);

namespace Tests\Unit\Observers;

use App\Enums\v1\CacheKey;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    $this->user = User::factory()->create([
        'name' => 'TestUser',
    ]);
    $this->profile = UserProfile::factory()->create([
        'user_id' => $this->user->id,
    ]);
});

it('clears cache when profile is created', function () {
    // Set up cache values
    Cache::put(CacheKey::USERS->value, 'test-users');
    Cache::put(CacheKey::USER->with($this->user->name), 'test-user');

    // Create a new profile (this will trigger the observer)
    UserProfile::factory()->create([
        'user_id' => $this->user->id,
    ]);

    // Assert cache was cleared
    expect(Cache::has(CacheKey::USERS->value))->toBeFalse()
        ->and(Cache::has(CacheKey::USER->with($this->user->name)))->toBeFalse();
});

it('clears cache when profile is updated', function () {
    // Set up cache values
    Cache::put(CacheKey::USERS->value, 'test-users');
    Cache::put(CacheKey::USER->with($this->user->name), 'test-user');

    // Update the profile
    $this->profile->bio = 'Updated bio';
    $this->profile->save();

    // Assert cache was cleared
    expect(Cache::has(CacheKey::USERS->value))->toBeFalse()
        ->and(Cache::has(CacheKey::USER->with($this->user->name)))->toBeFalse();
});

it('clears cache when profile is deleted', function () {
    // Set up cache values
    Cache::put(CacheKey::USERS->value, 'test-users');
    Cache::put(CacheKey::USER->with($this->user->name), 'test-user');

    // Delete the profile
    $this->profile->delete();

    // Assert cache was cleared
    expect(Cache::has(CacheKey::USERS->value))->toBeFalse()
        ->and(Cache::has(CacheKey::USER->with($this->user->name)))->toBeFalse();
});

it('clears cache when profile is updated regardless of case sensitivity', function () {

    // Set up cache values with different cases
    Cache::put(CacheKey::USER->with('testuser'), 'test-user-lower');
    Cache::put(CacheKey::USER->with('TESTUSER'), 'test-user-upper');
    Cache::put(CacheKey::USER->with('TestUser'), 'test-user-mixed');

    // Update the profile
    $this->profile->bio = 'Updated bio';
    $this->profile->save();

    // Assert cache was cleared for all case variations
    expect(Cache::has(CacheKey::USER->with('testuser')))->toBeFalse()
        ->and(Cache::has(CacheKey::USER->with('TESTUSER')))->toBeFalse()
        ->and(Cache::has(CacheKey::USER->with('TestUser')))->toBeFalse();
});
