<?php

namespace App\Http\Controllers\v1\User;

use App\Http\Resources\v1\User\UserResource;
use App\Models\User;
use App\Actions\v1\User\UpdateUserProfile;
use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\User\UpdateUserProfileRequest;
use App\Policies\v1\UserPolicy;
use Illuminate\Support\Facades\Gate;

class UserProfileController extends ApiController
{
    protected string $policyClass = UserPolicy::class;

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserProfileRequest $request, User $user, UpdateUserProfile $updateUserProfile): UserResource
    {
        Gate::authorize('update', $user);

        $updateUserProfile->execute($user, $request->validated());

        return new UserResource($user);
    }
}
