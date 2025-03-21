<?php

namespace App\Http\Controllers\v1\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController
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
    public function __invoke(Request $request): JsonResponse
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerate();

        return response()->json(['message' => 'Logout successful']);
    }
}
