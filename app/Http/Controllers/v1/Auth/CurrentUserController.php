<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Auth;

use App\Http\Resources\v1\User\CurrentUserResource;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

final class CurrentUserController
{
    public function __invoke(): CurrentUserResource
    {
        Cache::flush();
        Auth::user()?->load([
            'profile',
            'lists' => function (HasMany $query) {
                $query->orderBy('created_at', 'desc');
            },
        ]);

        return new CurrentUserResource(Auth::user());
    }
}
