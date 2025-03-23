<?php

declare(strict_types=1);

namespace App\Actions\v1\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateUser
{
    /**
     * Create a new user.
     *
     * @param  array<string, mixed>  $data
     */
    public function execute(array $data): User
    {
        // Refresh is needed otherwise default db values not passed show null
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'password' => Hash::make($data['password']),
        ])->refresh();
    }
}
