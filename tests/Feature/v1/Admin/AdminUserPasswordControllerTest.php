<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->user = User::factory()->create(['is_admin' => false, 'email' => 'user@example.com']);
    $this->otherUser = User::factory()->create(['is_admin' => false, 'email' => 'otheruser@example.com']);
});

describe('update', function () {
    it('allows admin to update user password', function () {
        login($this->admin)->patchJson("/v1/admin/users/{$this->user->name}/password", [
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ])->assertNoContent();

        $this->assertTrue(Hash::check('newpassword', $this->user->fresh()->password));
    });

    it('prevents a non-admin user from updating password', function () {
        login($this->user)->patchJson("/v1/admin/users/{$this->otherUser->name}/password", [
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ])->assertForbidden();
    });

    it('prevents a guest from updating password', function () {
        guest()->patchJson("/v1/admin/users/{$this->user->name}/password", [
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ])->assertUnauthorized();
    });
});
