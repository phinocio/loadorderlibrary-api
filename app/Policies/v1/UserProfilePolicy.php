<?php

declare(strict_types=1);

namespace App\Policies\v1;

use App\Models\User;

final class UserProfilePolicy
{
    public function view(?User $user, User $model): bool
    {
        return true;
    }

    public function update(User $user, User $model): bool
    {
        return $user->is($model);
    }
}
