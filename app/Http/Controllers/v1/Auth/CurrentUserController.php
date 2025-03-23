<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Auth;

use App\Http\Resources\v1\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CurrentUserController
{
    public function __invoke(): JsonResponse
    {
        return response()->json(
            new UserResource(Auth::user()),
            Response::HTTP_OK
        );
    }
}
