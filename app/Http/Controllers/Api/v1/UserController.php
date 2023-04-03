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
		return response()->json(['message' => 'not implemented'], 501);
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
