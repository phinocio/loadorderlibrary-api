<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Resources\v1\LoadOrderResource;
use App\Http\Resources\v1\UserResource;
use App\Models\LoadOrder;
use App\Models\User;
use App\Policies\v1\UserPolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;
use Throwable;

class UserController extends ApiController
{
    protected string $policyClass = UserPolicy::class;

    public function show(User $user): UserResource
    {
        Gate::authorize('view', User::class);

        return new UserResource(auth()->user());
    }

    /** @mixin User */
    public function lists(): AnonymousResourceCollection
    {
        Gate::authorize('view', User::class);
        $lists = LoadOrder::whereUserId(auth()->user()->id)->orderBy('created_at', 'desc')->get();

        return LoadOrderResource::collection($lists);
    }

    /**
     * Remove the specified resource from storage.
     */
    /** @mixin User */
    public function destroy(User $user): JsonResponse
    {
        Gate::authorize('delete', $user);
        try {
            $user->tokens()->delete();
            $user->delete();

            return response()->json(null, 204);
        } catch (Throwable $th) {
            return response()->json(['message' => 'something went wrong deleting the user. Please let Phinocio know.', 'error' => $th->getMessage()], 500);
        }
    }
}
