<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Auth;

use App\Http\Requests\v1\Auth\LoginRequest;
use App\Http\Resources\v1\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

final class LoginController
{
    public function __invoke(LoginRequest $request): JsonResponse
    {
        if (Auth::attempt($request->validated())) {
            session()->regenerate();

            return new UserResource(Auth::user())->response()->setStatusCode(Response::HTTP_OK);
        }

        return response()->json(['message' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
    }
}
