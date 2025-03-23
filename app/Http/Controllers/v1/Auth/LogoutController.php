<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class LogoutController
{
    public function __invoke(Request $request): JsonResponse
    {
        Auth::logout();

        $request->session()->invalidate();

        return response()->json(['message' => 'Logout successful'], Response::HTTP_NO_CONTENT);
    }
}
