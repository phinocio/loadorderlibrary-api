<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\User;

use App\Actions\v1\User\DeleteUser;
use App\Actions\v1\User\UpdateUser;
use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\User\UpdateUserRequest;
use App\Http\Resources\v1\User\CurrentUserResource;
use App\Models\User;
use App\Policies\v1\UserPolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

final class UserController extends ApiController
{
    protected string $policyClass = UserPolicy::class;

    public function update(UpdateUserRequest $request, User $user, UpdateUser $updateUser): CurrentUserResource
    {
        Gate::authorize('update', $user);

        /** @var array{email?: string|null} $data */
        $data = $request->validated();
        $user = $updateUser->execute($user, $data);

        return new CurrentUserResource($user->load([
            'profile',
            'lists',
        ]));
    }

    public function destroy(User $user, DeleteUser $deleteUser): JsonResponse
    {
        Gate::authorize('delete', $user);

        $deleteUser->execute($user);
        session()->invalidate();
        session()->regenerateToken();

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
