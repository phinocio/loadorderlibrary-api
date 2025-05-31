<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->password = 'password';
    $this->user = User::factory()->create(['password' => Hash::make($this->password)]);
    $this->otherUser = User::factory()->create(['password' => Hash::make($this->password)]);

    // Create API tokens with different abilities
    $this->readToken = $this->user->createToken('Read Token', ['read'])->plainTextToken;
    $this->updateToken = $this->user->createToken('Update Token', ['update'])->plainTextToken;
});

describe('GET routes', function () {
    it('allows unauthenticated access to GET /v1/users/{name}/profile (public route)', function () {
        $this->getJson("/v1/users/{$this->user->name}/profile")
            ->assertOk();
    });

    it('allows API tokens for GET /v1/users/{name}/profile', function () {
        $this->withHeader('Authorization', 'Bearer '.$this->readToken)
            ->getJson("/v1/users/{$this->user->name}/profile")
            ->assertOk();
    });

    it('allows session auth for GET /v1/users/{name}/profile', function () {
        login($this->user)
            ->getJson("/v1/users/{$this->user->name}/profile")
            ->assertOk();
    });
});

describe('PATCH routes', function () {
    it('rejects API tokens for PATCH /v1/users/{name} (auth middleware)', function () {
        $this->withHeader('Authorization', 'Bearer '.$this->updateToken)
            ->patchJson("/v1/users/{$this->user->name}", [
                'email' => 'newemail@example.com',
            ])
            ->assertUnauthorized();
    });

    it('allows session auth for PATCH /v1/users/{name}', function () {
        login($this->user)
            ->patchJson("/v1/users/{$this->user->name}", [
                'email' => 'newemail@example.com',
            ])
            ->assertOk();
    });

    it('rejects unauthenticated requests for PATCH /v1/users/{name}', function () {
        $this->patchJson("/v1/users/{$this->user->name}", [
            'email' => 'newemail@example.com',
        ])
            ->assertUnauthorized();
    });

    it('rejects API tokens for PATCH /v1/users/{name}/password', function () {
        $this->withHeader('Authorization', 'Bearer '.$this->updateToken)
            ->patchJson("/v1/users/{$this->user->name}/password", [
                'current_password' => $this->password,
                'password' => 'newpassword',
                'password_confirmation' => 'newpassword',
            ])
            ->assertUnauthorized();
    });

    it('allows session auth for PATCH /v1/users/{name}/password', function () {
        login($this->user)
            ->patchJson("/v1/users/{$this->user->name}/password", [
                'current_password' => $this->password,
                'password' => 'newpassword',
                'password_confirmation' => 'newpassword',
            ])
            ->assertNoContent();
    });

    it('rejects API tokens for PATCH /v1/users/{name}/profile', function () {
        $this->withHeader('Authorization', 'Bearer '.$this->updateToken)
            ->patchJson("/v1/users/{$this->user->name}/profile", [
                'bio' => 'Updated bio',
            ])
            ->assertUnauthorized();
    });

    it('allows session auth for PATCH /v1/users/{name}/profile', function () {
        login($this->user)
            ->patchJson("/v1/users/{$this->user->name}/profile", [
                'bio' => 'Updated bio',
            ])
            ->assertOk();
    });
});

describe('DELETE routes', function () {
    it('rejects API tokens for DELETE /v1/users/{name}', function () {
        $this->withHeader('Authorization', 'Bearer '.$this->updateToken)
            ->deleteJson("/v1/users/{$this->user->name}")
            ->assertUnauthorized();
    });

    it('allows session auth for DELETE /v1/users/{name}', function () {
        login($this->user)
            ->deleteJson("/v1/users/{$this->user->name}")
            ->assertNoContent();
    });

    it('rejects unauthenticated requests for DELETE /v1/users/{name}', function () {
        $this->deleteJson("/v1/users/{$this->user->name}")
            ->assertUnauthorized();
    });
});

describe('authorization and edge cases', function () {
    it('prevents users from updating other users via API tokens', function () {
        $this->withHeader('Authorization', 'Bearer '.$this->writeToken)
            ->patchJson("/v1/users/{$this->otherUser->name}", [
                'email' => 'hacker@example.com',
            ])
            ->assertUnauthorized();
    });

    it('prevents users from updating other users via session', function () {
        login($this->user)
            ->patchJson("/v1/users/{$this->otherUser->name}", [
                'email' => 'hacker@example.com',
            ])
            ->assertForbidden();
    });

    it('handles invalid tokens gracefully', function () {
        $this->withHeader('Authorization', 'Bearer invalid-token')
            ->patchJson("/v1/users/{$this->user->name}", [
                'email' => 'test@example.com',
            ])
            ->assertUnauthorized();
    });
});
