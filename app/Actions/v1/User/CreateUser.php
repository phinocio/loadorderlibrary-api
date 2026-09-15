<?php

declare(strict_types=1);

namespace App\Actions\v1\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

final class CreateUser
{
    /** @param  array{
     * name: string,
     * password: string
     * }  $data
     * @param  array<string, string>  $profileInfo
     */
    public function execute(array $data, bool $withPassword = true, array $profileInfo = []): User
    {
        $user = User::query()->create([
            'name' => $data['name'],
            'password' => $withPassword ? Hash::make($data['password']) : null,
        ]);

        $user->profile()->create(
            array_merge(['user_id' => $user->id], $profileInfo)
        );

        return $user->refresh();
    }
}
