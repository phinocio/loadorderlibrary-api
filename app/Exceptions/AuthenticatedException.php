<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final class AuthenticatedException extends Exception
{
    /** Render the exception as an HTTP response. */
    public function render(Request $request): JsonResponse
    {
        return response()->json(['message' => $this->getMessage()], Response::HTTP_FORBIDDEN);
    }
}
