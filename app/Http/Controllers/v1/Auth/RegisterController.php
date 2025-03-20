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
        $user = User::create($request->validated());

        Auth::login($user);

        return response()->json([
            'message' => 'User created successfully',
            'user' => new UserResource($user),
        ], 201);
    }
}
