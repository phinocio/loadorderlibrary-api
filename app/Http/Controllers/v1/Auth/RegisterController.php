<?php

namespace App\Http\Controllers\v1\Auth;

use App\Http\Requests\v1\RegisterRequest;
use App\Http\Resources\v1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class RegisterController
{
    public function __invoke(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::create($validated);
        $user = $user->refresh();

        Auth::login($user);

        session()->regenerate();

        return response()->json([
            'message' => 'User created successfully',
            'user' => new UserResource($user),
        ], 201);
    }
}
