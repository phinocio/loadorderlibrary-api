<?php

declare(strict_types=1);

namespace App\Observers\v1;

use App\Enums\v1\CacheKey;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Cache;

final class UserProfileObserver
{
    /** Handle the UserProfile "created" event. */
    public function created(UserProfile $userProfile): void
    {
        Cache::forget(CacheKey::USERS->value);
        Cache::forget(CacheKey::USER->with($userProfile->user->name));
    }

    /** Handle the UserProfile "updated" event. */
    public function updated(UserProfile $userProfile): void
    {
        Cache::forget(CacheKey::USERS->value);
        Cache::forget(CacheKey::USER->with($userProfile->user->name));
    }

    /** Handle the UserProfile "deleted" event. */
    public function deleted(UserProfile $userProfile): void
    {
        Cache::forget(CacheKey::USERS->value);
        Cache::forget(CacheKey::USER->with($userProfile->user->name));
    }
}
