<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TokenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return auth()->user()->tokens;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        if ($request->bearerToken()) {
            return response()->json(['message' => 'API Tokens can only be deleted through the website itself.'], 401);
        }

        auth()->user()->tokens->find($id)->delete();

        return response()->json(null, 204);
    }
}
