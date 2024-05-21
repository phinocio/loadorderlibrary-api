<?php

namespace App\Policies\v1;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->id === auth()->user()->id;
    }

    public function view(User $user): bool
    {
        return $user->id === auth()->user()->id;
    }

    public function delete(User $user, User $model): bool
    {
        return $user->id === $model->id;
    }
}
