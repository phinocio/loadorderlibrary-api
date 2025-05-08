<?php

declare(strict_types=1);

namespace App\Policies\v1;

use App\Models\LoadOrder;
use App\Models\User;

final class LoadOrderPolicy
{
    /** Determine whether the user can view any models. */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /** Determine whether the user can view the model. */
    public function view(?User $user, LoadOrder $loadOrder): bool
    {
        return true;
    }

    /** Determine whether the user can create models. */
    public function create(?User $user): bool
    {
        return true;
    }

    /** Determine whether the user can update the model. */
    public function update(User $user, LoadOrder $loadOrder): bool
    {
        return $user->id === $loadOrder->user_id;
    }

    /** Determine whether the user can delete the model. */
    public function delete(User $user, LoadOrder $loadOrder): bool
    {
        return $user->isAdmin() || $user->id === $loadOrder->user_id;
    }
}
