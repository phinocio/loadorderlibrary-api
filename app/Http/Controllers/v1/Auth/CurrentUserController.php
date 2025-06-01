<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Auth;

use App\Http\Resources\v1\User\CurrentUserResource;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

final class CurrentUserController
{
    public function __invoke(): CurrentUserResource
    {
        $user = Auth::user()?->load([
            'profile',
            'lists' => function (HasMany $query) {
                $query->orderBy('created_at', 'desc');
            },
        ]);

        return new CurrentUserResource($user);
    }
}
