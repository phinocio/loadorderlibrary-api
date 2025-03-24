<?php

declare(strict_types=1);

use App\Models\User;

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
