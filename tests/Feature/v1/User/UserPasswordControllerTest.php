<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->password = 'password';
    $this->admin = User::factory()->create(['is_admin' => true, 'password' => Hash::make($this->password)]);
    $this->user = User::factory()->create(['is_admin' => false, 'email' => 'user@example.com', 'password' => Hash::make($this->password)]);
    $this->otherUser = User::factory()->create(['is_admin' => false, 'email' => 'otheruser@example.com', 'password' => Hash::make($this->password)]);
});

describe('update', function () {
    it('allows user to update their password', function () {
        login($this->user)
            ->patchJson("/v1/users/{$this->user->name}/password", [
                'current_password' => $this->password,
                'password' => 'newpassword',
                'password_confirmation' => 'newpassword',
            ])
            ->assertNoContent();

        $this->assertTrue(Hash::check('newpassword', $this->user->fresh()->password));
    });

    it('validates password confirmation', function () {
        login($this->user)
            ->patchJson("/v1/users/{$this->user->name}/password", [
                'current_password' => $this->password,
                'password' => 'newpassword',
                'password_confirmation' => 'wrongconfirmation',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    });

    it('validates current password', function () {
        login($this->user)
            ->patchJson("/v1/users/{$this->user->name}/password", [
                'current_password' => 'wrongpassword',
                'password' => 'newpassword',
                'password_confirmation' => 'newpassword',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['current_password']);
    });

    it('prevents user from updating another user password', function () {
        login($this->user)
            ->patchJson("/v1/users/{$this->otherUser->name}/password", [
                'current_password' => $this->password,
                'password' => 'newpassword',
                'password_confirmation' => 'newpassword',
            ])
            ->assertForbidden();
    });

    it('prevents guest from updating password', function () {
        guest()
            ->patchJson("/v1/users/{$this->user->name}/password", [
                'current_password' => $this->password,
                'password' => 'newpassword',
                'password_confirmation' => 'newpassword',
            ])
            ->assertUnauthorized();
    });
});
