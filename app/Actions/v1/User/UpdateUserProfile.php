<?php

declare(strict_types=1);

namespace App\Actions\v1\User;

use App\Models\User;

final class UpdateUserProfile
{
    /**
     * @param array{
     *     bio?: string,
     *     discord?: string,
     *     kofi?: string,
     *     patreon?: string,
     *     website?: string} $data
     */
    public function execute(User $user, array $data): User
    {
        $user->profile()->updateOrCreate(['user_id' => $user->id], $data);

        return $user->load([
            'profile',
            'lists',
        ]);
    }
}
