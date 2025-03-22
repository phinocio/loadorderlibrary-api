<?php

declare(strict_types=1);

namespace App\Exceptions\v1;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthenticatedException extends Exception
{
    /** Report the exception. */
    public function report(): void
    {
        Log::error('AuthenticatedException: '.$this->getMessage());
    }

    /** Render the exception as an HTTP response. */
    public function render(Request $request): JsonResponse
    {
        return response()->json(['message' => 'You cannot access this route while logged in'], 403);
    }
}
