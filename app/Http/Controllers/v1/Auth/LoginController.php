<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Auth;

use App\Http\Requests\v1\Auth\LoginRequest;
use App\Http\Resources\v1\User\CurrentUserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

final class LoginController
{
    public function __invoke(LoginRequest $request): CurrentUserResource|JsonResponse
    {
        $data = $request->validated();
        if (Auth::attempt([
            'name' => $data['name'],
            'password' => $data['password'],
        ], (bool) ($data['remember'] ?? false))) {
            session()->regenerate();

            return new CurrentUserResource(Auth::user()?->load('profile'));
        }

        return response()->json(['message' => 'Invalid credentials'], JsonResponse::HTTP_UNAUTHORIZED);
    }
}
