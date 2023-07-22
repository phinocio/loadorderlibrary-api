<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\LoadOrderResource;
use App\Http\Resources\v1\UserResource;
use App\Models\LoadOrder;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Throwable;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): UserResource
    {
        return new UserResource(auth()->user());
    }

    /**
     * Display the specified resource.
     */
    public function show(): UserResource
    {
        return new UserResource(auth()->user());
    }

    /** @mixin User */
    public function lists(): AnonymousResourceCollection
    {
        $lists = LoadOrder::whereUserId(auth()->user()->id)->orderBy('created_at', 'desc')->get();

        return LoadOrderResource::collection($lists);
    }

    /**
     * Remove the specified resource from storage.
     */
    /** @mixin User */
    public function destroy(User $user): JsonResponse
    {
        try {
            if (auth()->user()->id === $user->id || auth()->user()->isAdmin()) {
                $user->delete();

                return response()->json(null, 204);
            } else {
                return response()->json(['message' => 'Unauthorized.'], 401);
            }
        } catch (Throwable $th) {
            return response()->json(['message' => 'something went wrong deleting the user. Please let Phinocio know.', 'error' => $th->getMessage()], 500);
        }
    }
}
