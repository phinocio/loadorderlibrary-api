<?php

declare(strict_types=1);

namespace Tests\Unit\Model;

use App\Models\User;
use App\Models\UserProfile;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('to array', function () {
    $profile = UserProfile::create([
        'user_id' => $this->user->id,
        'bio' => 'Test bio',
        'discord' => 'https://discord.com',
        'kofi' => 'https://ko-fi.com',
        'patreon' => 'https://patreon.com',
        'website' => 'https://website.com',
    ]);

    $array = $profile->toArray();

    expect($array)->toHaveKeys([
        'id',
        'user_id',
        'bio',
        'discord',
        'kofi',
        'patreon',
        'website',
        'created_at',
        'updated_at',
    ]);
});

test('user relationship', function () {
    $profile = UserProfile::create([
        'user_id' => $this->user->id,
        'bio' => 'Test bio',
        'discord' => 'https://discord.com',
        'kofi' => 'https://ko-fi.com',
        'patreon' => 'https://patreon.com',
        'website' => 'https://website.com',
    ]);

    $user = $profile->user;

    expect($user)->toBeInstanceOf(User::class);
});
