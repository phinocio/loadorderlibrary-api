<?php

declare(strict_types=1);

namespace App\Policies\v1;

use App\Models\User;

final class UserPolicy
{
    /** Determine whether the user can update the model. */
    public function update(User $user, User $model): bool
    {
        return $user->isAdmin() || $user->is($model);
    }

    /** Determine whether the user can delete the model. */
    public function delete(User $user, User $model): bool
    {
        return $user->isAdmin() || $user->is($model);
    }
}
