<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\User;

use App\Actions\v1\User\DeleteUser;
use App\Http\Controllers\ApiController;
use App\Http\Resources\v1\UserResource;
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

        return UserResource::collection(User::all())->response();
    }

    // TODO: Change to passing string name so we can cache the user
    // since route model binding will not cache it.
    public function show(User $user): JsonResponse
    {
        Gate::authorize('view', $user);

        return response()->json(new UserResource($user), Response::HTTP_OK);
    }

    public function destroy(User $user, DeleteUser $deleteUser): JsonResponse
    {
        Gate::authorize('delete', $user);

        $deleteUser->execute($user);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
