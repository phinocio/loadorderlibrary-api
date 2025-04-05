<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Auth;

use App\Http\Resources\v1\User\CurrentUserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

final class CurrentUserController
{
    public function __invoke(): JsonResponse
    {
        return (new CurrentUserResource(Auth::user()?->load('profile')))->response()->setStatusCode(Response::HTTP_OK);
    }
}
