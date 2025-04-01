<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\UserProfile;

test('to array', function () {
    $user = User::factory()->create()->refresh();

    $array = $user->toArray();

    expect($array)->toHaveKeys([
        'id',
        'name',
        'email',
        'is_verified',
        'is_admin',
        'created_at',
        'updated_at',
    ]);
});

test('profile relationship', function () {
    $user = User::factory()->create()->refresh();

    $profile = $user->profile;

    expect($profile)->toBeInstanceOf(UserProfile::class);
});
