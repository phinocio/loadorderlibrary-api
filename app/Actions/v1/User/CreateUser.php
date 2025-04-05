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
     */
    public function execute(array $data): User
    {
        $user = User::query()->create([
            'name' => $data['name'],
            'password' => Hash::make($data['password']),
        ]);

        $user->profile()->create([
            'user_id' => $user->id,
        ]);

        return $user->refresh();
    }
}
