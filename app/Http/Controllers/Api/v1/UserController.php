<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\LoadOrderResource;
use App\Http\Resources\v1\UserResource;
use App\Models\LoadOrder;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new UserResource(auth()->user());
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        return new UserResource(auth()->user());
    }

    public function lists()
    {
        $lists = LoadOrder::whereUserId(auth()->user()->id)->orderBy('created_at', 'desc')->get();

        return LoadOrderResource::collection($lists);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            if (auth()->user()->id === $user->id || auth()->user()->isAdmin()) {
                $user->delete();

                return response()->json(null, 204);
            } else {
                return response()->json(['message' => 'Unauthorized.'], 401);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => 'something went wrong deleting the user. Please let Phinocio know.', 'error' => $th->getMessage()], 500);
        }
    }
}
