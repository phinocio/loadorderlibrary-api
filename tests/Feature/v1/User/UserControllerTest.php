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
    it('allows user to update themselves', function () {
        login($this->user)
            ->patchJson("/v1/users/{$this->user->name}", [
                'email' => 'newemail2@example.com',
            ])
            ->assertOk()
            ->assertExactJsonStructure(['data' => getCurrentUserJsonStructure()]);

        $this->assertDatabaseHas('users', [
            'email' => 'newemail2@example.com',
        ]);
    });

    it('allows user to remove their email', function () {
        login($this->user)
            ->patchJson("/v1/users/{$this->user->name}", ['email' => null])
            ->assertOk()
            ->assertExactJsonStructure(['data' => getCurrentUserJsonStructure()]);

        $this->assertDatabaseHas('users', [
            'name' => $this->user->name,
            'email' => null,
        ]);
    });

    // Admin must use Admin routes
    it('prevents admin from updating a user', function () {
        login($this->admin)
            ->patchJson("/v1/users/{$this->user->name}", ['email' => 'newemail@example.com'])
            ->assertForbidden();
    });

    it('prevents a user from updating another user', function () {
        login($this->otherUser)
            ->patchJson("/v1/users/{$this->user->name}", ['email' => 'newemail3@example.com'])
            ->assertForbidden();
    });

    it('prevents a guest from updating a user', function () {
        guest()
            ->patchJson("/v1/users/{$this->user->name}", ['email' => 'newemail3@example.com'])
            ->assertUnauthorized();
    });

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

    it('validates password confirmation when updating password', function () {
        login($this->user)
            ->patchJson("/v1/users/{$this->user->name}/password", [
                'password' => 'newpassword',
                'password_confirmation' => 'wrongconfirmation',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    });

    it('prevents updating with invalid email format', function () {
        login($this->user)
            ->patchJson("/v1/users/{$this->user->name}", ['email' => 'invalid-email'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    });
});

describe('destroy', function () {
    it('allows user to delete themselves', function () {
        login($this->user)->deleteJson("/v1/users/{$this->user->name}")->assertNoContent();
        $this->assertDatabaseMissing('users', [
            'name' => $this->user->name,
        ]);
    });

    // Admin must use Admin routes
    it('prevents admin from deleting a user', function () {
        login($this->admin)->deleteJson("/v1/users/{$this->user->name}")->assertForbidden();
    });

    it('prevents a user from deleting another user', function () {
        login($this->otherUser)->deleteJson("/v1/users/{$this->user->name}")->assertForbidden();
    });

    it('prevents a guest from deleting a user', function () {
        guest()->deleteJson("/v1/users/{$this->user->name}")->assertUnauthorized();
    });
});
