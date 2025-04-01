<?php

declare(strict_types=1);

namespace App\Policies\v1;

use App\Models\User;

final class UserProfilePolicy
{
    /** Determine whether the user can update the model. */
    public function update(User $user, User $model): bool
    {
        return $user->is($model);
    }
}
