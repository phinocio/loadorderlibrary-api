<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Admin;

use App\Actions\v1\User\DeleteUser;
use App\Actions\v1\User\UpdateUser;
use App\Enums\v1\CacheKey;
use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\User\UpdateUserRequest;
use App\Http\Resources\v1\User\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

final class AdminUserController extends ApiController
{
    // No policy because this entire controller is protected by middleware

    public function index(): JsonResponse
    {
        $users = Cache::rememberForever(
            CacheKey::USERS->value,
            fn () => User::all()
        );

        return UserResource::collection($users)->response()->setStatusCode(Response::HTTP_OK);
    }

    public function update(UpdateUserRequest $request, User $user, UpdateUser $updateUser): JsonResponse
    {
        /** @var array<int, array{email?: string|null, password?: string}> $data */
        $data = $request->validated();
        $user = $updateUser->execute($user, $data);

        return (new UserResource($user))->response()->setStatusCode(Response::HTTP_OK);
    }

    public function destroy(User $user, DeleteUser $deleteUser): JsonResponse
    {
        $deleteUser->execute($user);

        return (new UserResource($user))->response()->setStatusCode(Response::HTTP_NO_CONTENT);
    }
}
