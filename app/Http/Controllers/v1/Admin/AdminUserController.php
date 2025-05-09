<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Admin;

use App\Actions\v1\User\DeleteUser;
use App\Actions\v1\User\UpdateUser;
use App\Enums\v1\CacheKey;
use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\Admin\AdminUpdateUserRequest;
use App\Http\Resources\v1\User\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

final class AdminUserController extends ApiController
{
    // No policy because this entire controller is protected by middleware

    public function index(): AnonymousResourceCollection
    {
        $users = Cache::rememberForever(
            CacheKey::USERS->value,
            fn () => User::all()
        );

        return UserResource::collection($users);
    }

    public function show(string $username): UserResource
    {
        $user = Cache::rememberForever(
            CacheKey::USER->with($username),
            fn () => User::query()->where('name', $username)->with('profile')->firstOrFail()
        );

        return new UserResource($user);
    }

    public function update(AdminUpdateUserRequest $request, User $user, UpdateUser $updateUser): UserResource
    {
        /** @var array<int, array{email?: string|null, verified?: bool}> $data */
        $data = $request->validated();
        $user = $updateUser->execute($user, $data);

        return new UserResource($user);
    }

    public function destroy(User $user, DeleteUser $deleteUser): JsonResponse
    {
        $deleteUser->execute($user);

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
