<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class LogoutController
{
    public function __invoke(Request $request): JsonResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
