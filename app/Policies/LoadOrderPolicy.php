<?php

namespace App\Policies;

use App\Models\LoadOrder;
use App\Models\User;

class LoadOrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true; // Anyone is able to view lists
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user): bool
    {
        // TODO: Eventually I will change list visibility to be more granular.
        return true; // Anyone is able to view a specific list
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(?User $user): bool
    {
        return true; // Anyone is able to create a list
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LoadOrder $loadOrder): bool
    {
        return $user->id === $loadOrder->user_id; // Only the owner of a list can update it
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LoadOrder $loadOrder): bool
    {
        return $user->isAdmin() || $user->id === $loadOrder->user_id; // List owner or Admin can delete
    }
}
