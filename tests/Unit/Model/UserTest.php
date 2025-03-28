<?php

declare(strict_types=1);

use App\Http\Resources\v1\UserResource;
use App\Models\User;

test('to array', function () {
    $user = User::factory()->create()->refresh();

    $array = $user->toArray();

    expect($array)->toHaveKeys([
        'id',
        'name',
        'email',
        'bio',
        'discord',
        'kofi',
        'patreon',
        'website',
        'is_verified',
        'is_admin',
        'created_at',
        'updated_at',
    ]);
});