<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Auth;

use App\Actions\v1\User\CreateUser;
use App\Http\Requests\v1\Auth\RegisterRequest;
use App\Http\Resources\v1\User\CurrentUserResource;
use Illuminate\Support\Facades\Auth;

final class RegisterController
{
    public function __invoke(RegisterRequest $request, CreateUser $createUser): CurrentUserResource
    {
        /** @var array{name: string, password: string} $data */
        $data = $request->validated();
        $user = $createUser->execute($data);

        Auth::login($user);

        session()->regenerate();

        return new CurrentUserResource($user->load([
            'profile',
            'lists',
        ]));
    }
}
