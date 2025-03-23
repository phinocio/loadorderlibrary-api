<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\UpdateUserRequest;
use App\Http\Resources\v1\UserResource;
use App\Models\User;
use App\Policies\v1\UserPolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class UserController extends ApiController
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

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        Gate::authorize('update', $user);

        dd($request);

        $user->update($request->validated());

        return response()->json(new UserResource($user), Response::HTTP_OK);
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
