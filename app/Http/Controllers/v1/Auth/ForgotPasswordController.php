<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Auth;

use App\Http\Requests\v1\Auth\ForgotPasswordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;

final class ForgotPasswordController
{
    /** Handle the incoming request. */
    public function __invoke(ForgotPasswordRequest $request): JsonResponse
    {
        /** @var array{email: string} */
        $data = $request->validated();

        $status = Password::sendResetLink($data);

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['status' => 'reset_link_sent'], JsonResponse::HTTP_OK);
        }

        $message = match ($status) {
            Password::INVALID_USER => 'We could not find a user with that email address.',
            Password::RESET_THROTTLED => 'Please wait before retrying.',
            default => 'Unable to send password reset link.',
        };

        return response()->json([
            'status' => 'reset_link_failed',
            'message' => $message,
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
    }
}
