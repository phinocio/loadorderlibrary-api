<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\User;

use App\Actions\v1\User\UpdateUser;
use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\User\UpdateUserEmailRequest;
use App\Http\Resources\v1\UserResource;
use App\Models\User;
use App\Policies\v1\UserPolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

final class UserEmailController extends ApiController
{
    protected string $policyClass = UserPolicy::class;

    public function update(UpdateUserEmailRequest $request, User $user, UpdateUser $updateUser): JsonResponse
    {
        Gate::authorize('update', $user);

        $user = $updateUser->execute($user, $request->validated());

        return response()->json(new UserResource($user), Response::HTTP_OK);
    }

    public function destroy(User $user, UpdateUser $updateUser): JsonResponse
    {
        Gate::authorize('update', $user);

        $user = $updateUser->execute($user, ['email' => null]);

        return response()->json(new UserResource($user), Response::HTTP_OK);
    }
}
