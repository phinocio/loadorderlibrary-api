<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Auth;

use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\Auth\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

final class ResetPasswordController extends ApiController
{
    public function __invoke(ResetPasswordRequest $request): JsonResponse
    {
        /** @var array{token: string, email: string, password: string, password_confirmation: string} $credentials */
        $credentials = $request->validated();

        /** @var string $status */
        $status = Password::reset(
            $credentials,
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));
                $user->save();

                Auth::login($user);
                session()->regenerate();
                session()->regenerateToken();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['status' => 'password_reset_success'], Response::HTTP_OK);
        }

        $message = match ($status) {
            Password::INVALID_TOKEN => 'This password reset token is invalid.',
            Password::INVALID_USER => 'We could not find a user with that email address.',
            Password::RESET_THROTTLED => 'Please wait before retrying.',
            default => 'Unable to reset password.',
        };

        return response()->json([
            'status' => 'password_reset_failed',
            'message' => $message,
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
