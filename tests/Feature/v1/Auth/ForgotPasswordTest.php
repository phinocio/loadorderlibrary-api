<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Password;

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'test@example.com',
    ])->fresh();
});

it('sends password reset link with valid email', function () {
    $response = $this->postJson('/v1/forgot-password', [
        'email' => $this->user->email,
    ]);

    $response->assertOk()
        ->assertJson(['status' => 'reset_link_sent']);
});

it('fails with invalid email', function () {
    $response = $this->postJson('/v1/forgot-password', [
        'email' => 'nonexistent@example.com',
    ]);

    $response->assertUnprocessable()
        ->assertJson([
            'status' => 'reset_link_failed',
            'message' => 'We could not find a user with that email address.',
        ]);
});

it('fails with missing email', function () {
    $response = $this->postJson('/v1/forgot-password', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('fails when reset is throttled', function () {
    // Mock the Password facade to simulate throttling
    Password::shouldReceive('sendResetLink')
        ->once()
        ->andReturn(Password::RESET_THROTTLED);

    $response = $this->postJson('/v1/forgot-password', [
        'email' => $this->user->email,
    ]);

    $response->assertUnprocessable()
        ->assertJson([
            'status' => 'reset_link_failed',
            'message' => 'Please wait before retrying.',
        ]);
});

it('handles unknown error', function () {
    // Mock the Password facade to return an unknown status
    Password::shouldReceive('sendResetLink')
        ->once()
        ->andReturn('unknown_error');

    $response = $this->postJson('/v1/forgot-password', [
        'email' => $this->user->email,
    ]);

    $response->assertUnprocessable()
        ->assertJson([
            'status' => 'reset_link_failed',
            'message' => 'Unable to send password reset link.',
        ]);
});
