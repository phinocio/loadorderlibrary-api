<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\User;

use App\Actions\v1\User\UpdateUserProfile;
use App\Enums\v1\CacheKey;
use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\User\UpdateUserProfileRequest;
use App\Http\Resources\v1\User\CurrentUserResource;
use App\Http\Resources\v1\User\UserResource;
use App\Models\User;
use App\Policies\v1\UserProfilePolicy;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

final class UserProfileController extends ApiController
{
    protected string $policyClass = UserProfilePolicy::class;

    public function show(string $name): UserResource
    {
        $user = Cache::rememberForever(
            CacheKey::USER->with($name),
            fn () => User::query()->where('name', $name)->with(['profile', 'publicLists.game', 'publicLists.author'])->firstOrFail()
        );

        Gate::authorize('view', $user);

        return new UserResource($user);
    }

    public function update(UpdateUserProfileRequest $request, User $user, UpdateUserProfile $updateUserProfile): CurrentUserResource
    {
        Gate::authorize('update', $user);

        /** @var array<int, array{bio?: string, discord?: string, kofi?: string, patreon?: string, website?: string}> $data */
        $data = $request->validated();
        $updateUserProfile->execute($user, $data);
        $user->touch('updated_at');

        return new CurrentUserResource($user);
    }
}
