<?php

namespace App\Http\Controllers\v1\Auth;

use App\Http\Requests\v1\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LoginController
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Invoke the class instance.
     */
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if (Auth::check()) {
            return response()->json(['message' => 'User already logged in']);
        }

        if (Auth::attempt($validated)) {
            session()->regenerate();

            return response()->json(['message' => 'Login successful']);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);

    }
}
