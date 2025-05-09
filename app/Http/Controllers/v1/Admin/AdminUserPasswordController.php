<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Admin;

use App\Actions\v1\User\UpdateUser;
use App\Http\Requests\v1\Admin\AdminUpdateUserPasswordRequest;
use App\Models\User;
use Symfony\Component\HttpFoundation\JsonResponse;

final class AdminUserPasswordController
{
    public function update(AdminUpdateUserPasswordRequest $request, User $user, UpdateUser $updateUser): JsonResponse
    {
        /** @var array{password: string} $data */
        $data = ['password' => $request->validated('password')];
        $user = $updateUser->execute($user, $data);

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
