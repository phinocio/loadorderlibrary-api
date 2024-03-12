<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\LoadOrderResource;
use App\Http\Resources\v1\UserResource;
use App\Models\LoadOrder;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

    public function apiTokens()
    {
        return auth()->user()->tokens;
    }

    public function createApiToken(Request $request)
    {
        if ($request->bearerToken()) {
            return response()->json(['message' => 'API Tokens can only be created through the website itself.'], 401);
        }
        $request->validate([
            'token_name' => 'required',
        ]);

        $abilities = [];

        if ($request->create) {
            $abilities[] = 'create';
        }

        if ($request->read) {
            $abilities[] = 'read';
        }

        if ($request->update) {
            $abilities[] = 'update';
        }

        if ($request->delete) {
            $abilities[] = 'delete';
        }

        $token = $request->user()->createToken($request->token_name, $abilities);

        return [
            'token' => $token->plainTextToken,
        ];
    }

    public function destroyApiToken($tokenId)
    {
        auth()->user()->tokens->find($tokenId)->delete();

        return response()->json(null, 204);
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
