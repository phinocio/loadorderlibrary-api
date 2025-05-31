<?php

declare(strict_types=1);

namespace App\Policies\v1;

use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

final class ApiTokenPolicy
{
    /** Determine whether the user can view any tokens. */
    public function viewAny(User $user): bool
    {
        return true; // Users can view their own tokens
    }

    /** Determine whether the user can view the token. */
    public function view(User $user, PersonalAccessToken $token): bool
    {
        return $user->id === $token->tokenable_id && $token->tokenable_type === User::class;
    }

    /** Determine whether the user can create tokens. */
    public function create(User $user): bool
    {
        return true; // Users can create their own tokens
    }

    /** Determine whether the user can update the token. */
    public function update(User $user, PersonalAccessToken $token): bool
    {
        return $user->id === $token->tokenable_id && $token->tokenable_type === User::class;
    }

    /** Determine whether the user can delete the token. */
    public function delete(User $user, PersonalAccessToken $token): bool
    {
        return $user->id === $token->tokenable_id && $token->tokenable_type === User::class;
    }
}
