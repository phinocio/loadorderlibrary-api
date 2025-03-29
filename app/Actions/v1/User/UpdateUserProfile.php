<?php


declare(strict_types=1);

namespace App\Actions\v1\User;

use App\Models\User;

class UpdateUserProfile
{
    public function execute(User $user, array $data): User
    {
        $user->profile()->update($data);

        return $user;
    }
}

