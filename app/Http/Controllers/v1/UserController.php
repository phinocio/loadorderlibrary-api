<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1;

use App\Http\Controllers\ApiController;
use App\Http\Resources\v1\UserResource;
use App\Models\User;
use App\Policies\v1\UserPolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

    public function show(User $user): JsonResponse
    {
        return response()->json(new UserResource($user), Response::HTTP_OK);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $user->update($request->validated());

        return response()->json(new UserResource($user), Response::HTTP_OK);
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
