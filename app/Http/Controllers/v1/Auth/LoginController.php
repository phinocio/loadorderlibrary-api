<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Auth;

use App\Http\Requests\v1\LoginRequest;
use App\Http\Resources\v1\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LoginController
{
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if (Auth::attempt($validated)) {
            session()->regenerate();

            return response()->json([
                'message' => 'Login successful',
                'user' => new UserResource(Auth::user()),
            ]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }
}
