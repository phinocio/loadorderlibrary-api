<?php

declare(strict_types=1);

namespace App\Policies\v1;

use App\Models\User;

final class UserPolicy
{
    public function update(User $user, User $model): bool
    {
        return $user->is($model);
    }

    public function delete(User $user, User $model): bool
    {
        return $user->is($model);
    }
}
