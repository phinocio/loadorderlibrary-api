<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Admin\UpdateUserRequest;
use App\Http\Resources\v1\UserResource;
use App\Models\User;

class UserController extends Controller
{
    public function __construct()
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return UserResource::collection(User::all());
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        if (!array_key_exists('verified', $validated)) {
            $validated['verified'] = false;
        }

        // The user wants to remove email, but sending an empty string or value of null
        // for some reason doesn't get put into validated, despite nullable being a rule.
        if (!array_key_exists('email', $validated) && $request->get('email')) {
            $user->email = null;
        } else {
            $user->email = $validated['email'] ?? $user->email;
        }

        $user->is_verified = (bool)$validated['verified'];

        $user->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
