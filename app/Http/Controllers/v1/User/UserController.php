<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\User;

use App\Actions\v1\User\DeleteUser;
use App\Actions\v1\User\UpdateUser;
use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\User\UpdateUserRequest;
use App\Http\Resources\v1\User\UserResource;
use App\Models\User;
use App\Policies\v1\UserPolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

final class UserController extends ApiController
{
    protected string $policyClass = UserPolicy::class;

    public function index(): JsonResponse
    {
        Gate::authorize('viewAny', User::class);

        return UserResource::collection(User::all())->response()->setStatusCode(Response::HTTP_OK);
    }

    public function show(string $userName): JsonResponse
    {
        $user = User::where('name', $userName)->with('profile')->firstOrFail();
        Gate::authorize('view', $user);

        return new UserResource($user)->response()->setStatusCode(Response::HTTP_OK);
    }

    public function update(UpdateUserRequest $request, User $user, UpdateUser $updateUser): JsonResponse
    {
        Gate::authorize('update', $user);

        $user = $updateUser->execute($user, $request->validated());

        return new UserResource($user)->response()->setStatusCode(Response::HTTP_OK);
    }

    public function destroy(User $user, DeleteUser $deleteUser): JsonResponse
    {
        Gate::authorize('delete', $user);

        $deleteUser->execute($user);

        return new UserResource($user)->response()->setStatusCode(Response::HTTP_NO_CONTENT);
    }
}
