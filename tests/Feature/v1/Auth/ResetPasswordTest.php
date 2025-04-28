<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'test@example.com',
    ])->fresh();

    $this->token = Password::createToken($this->user);
});

it('resets password with valid token', function () {
    $response = $this->postJson('/v1/reset-password', [
        'token' => $this->token,
        'email' => $this->user->email,
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertOk()
        ->assertJson(['status' => 'password_reset_success']);

    $this->user->refresh();
    $this->assertTrue(Hash::check('newpassword123', $this->user->password));
    $this->assertAuthenticatedAs($this->user);
});

it('fails with invalid token', function () {
    $response = $this->postJson('/v1/reset-password', [
        'token' => 'invalid-token',
        'email' => $this->user->email,
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertUnprocessable()
        ->assertJson([
            'status' => 'password_reset_failed',
            'message' => 'This password reset token is invalid.',
        ]);
});

it('fails with invalid email', function () {
    $response = $this->postJson('/v1/reset-password', [
        'token' => $this->token,
        'email' => 'wrong@example.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertUnprocessable()
        ->assertJson([
            'status' => 'password_reset_failed',
            'message' => 'We could not find a user with that email address.',
        ]);
});

it('fails with non-matching password confirmation', function () {
    $response = $this->postJson('/v1/reset-password', [
        'token' => $this->token,
        'email' => $this->user->email,
        'password' => 'newpassword123',
        'password_confirmation' => 'different',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});

it('fails with missing required fields', function () {
    $response = $this->postJson('/v1/reset-password', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['token', 'email', 'password']);
});

it('handles unknown reset error', function () {
    // Mock the Password facade to return an unknown status
    Password::shouldReceive('reset')
        ->once()
        ->andReturn('unknown_error');

    $response = $this->postJson('/v1/reset-password', [
        'token' => $this->token,
        'email' => $this->user->email,
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertUnprocessable()
        ->assertJson([
            'status' => 'password_reset_failed',
            'message' => 'Unable to reset password.',
        ]);
});
