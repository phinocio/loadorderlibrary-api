<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
		try {
			$user->delete();
			/*
			 * TODO: A proper response
			 */
			return ['deleted'];
		} catch (\Throwable $th) {
			return $th->getMessage();
		}
    }
}
