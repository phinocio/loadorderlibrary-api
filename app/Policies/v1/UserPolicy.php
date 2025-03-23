<?php

declare(strict_types=1);

namespace App\Policies\v1;

use App\Models\User;

class UserPolicy
{
    /** Determine whether the user can view any models. */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /** Determine whether the user can view the model. */
    public function view(User $user, User $model): bool
    {
        return $user->isAdmin() || $user->id === $model->id;
    }

    /** Determine whether the user can create models. */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /** Determine whether the user can update the model. */
    public function update(User $user, User $model): bool
    {
        return $user->isAdmin() || $user->id === $model->id;
    }

    /** Determine whether the user can delete the model. */
    public function delete(User $user, User $model): bool
    {
        return $user->isAdmin() || $user->id === $model->id;
    }
}
