<?php

declare(strict_types=1);

namespace App\Observers\v1;

use App\Enums\v1\CacheKey;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

final class UserObserver
{
    /** Handle the User "created" event. */
    public function created(User $user): void
    {
        Cache::forget(CacheKey::USERS->value);
    }

    /** Handle the User "updated" event. */
    public function updated(User $user): void
    {
        Cache::forget(CacheKey::USERS->value);
        Cache::forget(CacheKey::USER->with($user->name));
    }

    /** Handle the User "deleted" event. */
    public function deleted(User $user): void
    {
        Cache::forget(CacheKey::USERS->value);
        Cache::forget(CacheKey::USER->with($user->name));
    }
}
