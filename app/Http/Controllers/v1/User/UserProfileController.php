<?php

namespace App\Http\Controllers\v1\User;

use App\Http\Resources\v1\User\UserResource;
use App\Models\User;
use App\Actions\v1\User\UpdateUserProfile;
use App\Http\Requests\v1\User\UpdateUserProfileRequest;

class UserProfileController
{
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserProfileRequest $request, User $user, UpdateUserProfile $updateUserProfile): UserResource
    {
        $updateUserProfile->execute($user, $request->validated());

        return new UserResource($user);
    }
}
