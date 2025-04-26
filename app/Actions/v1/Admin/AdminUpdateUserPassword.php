<?php

declare(strict_types=1);

namespace App\Actions\v1\Admin;

use App\Models\User;

final class AdminUpdateUserPassword
{
    /**
     * @param array{
     *     password: string,
     * } $data
     */
    public function execute(User $user, array $data): User
    {
        $user->update($data);

        return $user;
    }
}
