<?php

declare(strict_types=1);

namespace App\Actions\v1\User;

use App\Models\User;

final class RemoveEmail
{
    public function execute(User $user): User
    {
        $user->email = null;

        $user->save();

        return $user;
    }
}
