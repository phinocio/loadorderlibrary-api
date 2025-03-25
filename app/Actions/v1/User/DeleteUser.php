<?php

declare(strict_types=1);

namespace App\Actions\v1\User;

use App\Models\User;

final class DeleteUser
{
    public function execute(User $user): void
    {
        $user->delete();
    }
}
