<?php

declare(strict_types=1);

namespace App\Actions\v1\User;

use App\Models\User;

final class UpdateUser
{
    /** @param array{
     *      email?: string|null,
     *      bio?: string|null,
     *      discord?: string|null,
     *      kofi?: string|null,
     *      patreon?: string|null,
     *      website?: string|null
     *      } $data
     */
    public function execute(User $user, array $data): User
    {
        $user->update($data);

        return $user;
    }
}
