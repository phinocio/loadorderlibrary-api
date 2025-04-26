<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\User;

use App\Actions\v1\User\UpdateUser;
use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\User\UpdateUserPasswordRequest;
use App\Models\User;
use App\Policies\v1\UserPolicy;
use Gate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

final class UserPasswordController extends ApiController
{
    protected string $policyClass = UserPolicy::class;

    public function update(UpdateUserPasswordRequest $request, User $user, UpdateUser $updateUser): JsonResponse
    {
        Gate::authorize('update', $user);

        /** @var array{password: string} $data */
        $data = ['password' => $request->validated('password')];
        $user = $updateUser->execute($user, $data);

        // Re-authenticate the user after password change
        Auth::login($user);
        session()->regenerateToken();
        session()->regenerate();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
