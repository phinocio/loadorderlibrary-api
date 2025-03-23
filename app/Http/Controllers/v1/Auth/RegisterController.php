<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Auth;

use App\Http\Requests\v1\RegisterRequest;
use App\Http\Resources\v1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class RegisterController
{
    public function __invoke(RegisterRequest $request): JsonResponse
    {
        $user = User::create($request->validated());
        $user = $user->refresh();

        Auth::login($user);

        session()->regenerate();

        return response()->json(
            new UserResource($user),
            Response::HTTP_CREATED
        );
    }
}
