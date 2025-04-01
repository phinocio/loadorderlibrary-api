<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Auth;

use App\Actions\v1\User\CreateUser;
use App\Http\Requests\v1\Auth\RegisterRequest;
use App\Http\Resources\v1\User\CurrentUserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

final class RegisterController
{
    public function __construct(
        private readonly CreateUser $createUser
    ) {}

    public function __invoke(RegisterRequest $request): JsonResponse
    {
        $user = $this->createUser->execute($request->validated());

        Auth::login($user);

        session()->regenerate();

        return new CurrentUserResource($user->load('profile'))->response()->setStatusCode(Response::HTTP_CREATED);
    }
}
