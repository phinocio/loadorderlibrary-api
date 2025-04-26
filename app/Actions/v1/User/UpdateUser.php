<?php

declare(strict_types=1);

namespace App\Actions\v1\User;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

final class UpdateUser
{
    /**
     * @param array{
     *     email?: string|null,
     *     password?: string,
     * } $data
     */
    public function execute(User $user, array $data): User
    {
        $user->update($data);

        if (isset($data['password'])) {
            Auth::login($user);
            session()->regenerate();
            session()->regenerateToken();
        }

        return $user;
    }
}
