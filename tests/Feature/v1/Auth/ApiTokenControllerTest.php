<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
});

describe('index', function () {
    it('allows authenticated user to view their own tokens', function () {
        $this->user->createToken('Test Token 1', ['read']);
        $this->user->createToken('Test Token 2', ['create']);
        $this->otherUser->createToken('Other User Token', ['read']);

        $response = login($this->user)->getJson('/v1/api-tokens');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'abilities',
                        'last_used',
                        'expires',
                        'created',
                        'updated',
                    ],
                ],
            ])
            ->assertJsonCount(2, 'data');

        // Verify both tokens are present (order may vary)
        $tokenNames = collect($response->json('data'))->pluck('name')->toArray();
        expect($tokenNames)->toContain('Test Token 1', 'Test Token 2');
    });

    it('returns empty array when user has no tokens', function () {
        $response = login($this->user)->getJson('/v1/api-tokens');

        $response->assertOk()
            ->assertJsonStructure(['data'])
            ->assertJsonCount(0, 'data');
    });

    it('prevents unauthenticated users from viewing tokens', function () {
        guest()->getJson('/v1/api-tokens')
            ->assertUnauthorized();
    });
});

describe('store', function () {
    it('allows authenticated user to create a new token', function () {
        $response = login($this->user)->postJson('/v1/api-tokens', [
            'token_name' => 'My API Token',
            'abilities' => ['read', 'create'],
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token'])
            ->assertJsonPath('token', function ($token) {
                return is_string($token) && mb_strlen($token) > 0;
            });

        // Verify token was created in database
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $this->user->id,
            'tokenable_type' => User::class,
            'name' => 'My API Token',
            'abilities' => json_encode(['read', 'create']),
        ]);
    });

    it('creates token with single ability', function () {
        $response = login($this->user)->postJson('/v1/api-tokens', [
            'token_name' => 'Read Only Token',
            'abilities' => ['read'],
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token']);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $this->user->id,
            'name' => 'Read Only Token',
            'abilities' => json_encode(['read']),
        ]);
    });

    it('creates token with multiple abilities', function () {
        $response = login($this->user)->postJson('/v1/api-tokens', [
            'token_name' => 'Full Access Token',
            'abilities' => ['read', 'create', 'update', 'delete'],
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token']);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $this->user->id,
            'name' => 'Full Access Token',
            'abilities' => json_encode(['read', 'create', 'update', 'delete']),
        ]);
    });

    it('prevents unauthenticated users from creating tokens', function () {
        guest()->postJson('/v1/api-tokens', [
            'token_name' => 'Unauthorized Token',
            'abilities' => ['read'],
        ])->assertUnauthorized();
    });

    it('validates required fields', function () {
        login($this->user)->postJson('/v1/api-tokens', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['token_name', 'abilities']);
    });

    it('validates token_name is required', function () {
        login($this->user)->postJson('/v1/api-tokens', [
            'abilities' => ['read'],
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['token_name']);
    });

    it('validates abilities is required', function () {
        login($this->user)->postJson('/v1/api-tokens', [
            'token_name' => 'Test Token',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['abilities']);
    });

    it('validates token_name is a string', function () {
        login($this->user)->postJson('/v1/api-tokens', [
            'token_name' => ['not', 'a', 'string'],
            'abilities' => ['read'],
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['token_name']);
    });

    it('validates abilities is an array', function () {
        login($this->user)->postJson('/v1/api-tokens', [
            'token_name' => 'Test Token',
            'abilities' => 'not-an-array',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['abilities']);
    });

    it('validates token_name max length', function () {
        login($this->user)->postJson('/v1/api-tokens', [
            'token_name' => str_repeat('a', 256), // Over 255 characters
            'abilities' => ['read'],
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['token_name']);
    });

    it('validates abilities contain only allowed values', function () {
        login($this->user)->postJson('/v1/api-tokens', [
            'token_name' => 'Invalid Abilities Token',
            'abilities' => ['read', 'invalid'],
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['abilities.1']);
    });

    it('rejects old ability names', function () {
        login($this->user)->postJson('/v1/api-tokens', [
            'token_name' => 'Old Abilities Token',
            'abilities' => ['write'],
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['abilities.0']);

        login($this->user)->postJson('/v1/api-tokens', [
            'token_name' => 'Admin Ability Token',
            'abilities' => ['admin'],
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['abilities.0']);
    });

    it('accepts all valid ability combinations', function () {
        $validAbilities = [
            ['read'],
            ['create'],
            ['update'],
            ['delete'],
            ['read', 'create'],
            ['read', 'update'],
            ['read', 'delete'],
            ['create', 'update'],
            ['create', 'delete'],
            ['update', 'delete'],
            ['read', 'create', 'update'],
            ['read', 'create', 'delete'],
            ['read', 'update', 'delete'],
            ['create', 'update', 'delete'],
            ['read', 'create', 'update', 'delete'],
        ];

        foreach ($validAbilities as $index => $abilities) {
            $response = login($this->user)->postJson('/v1/api-tokens', [
                'token_name' => "Valid Token {$index}",
                'abilities' => $abilities,
            ]);

            $response->assertOk('Failed for abilities: '.implode(', ', $abilities));
        }
    });
});

describe('destroy', function () {
    it('allows user to delete their own token', function () {
        $token = $this->user->createToken('Test Token', ['read']);
        $tokenId = $token->accessToken->id;

        $response = login($this->user)->deleteJson("/v1/api-tokens/{$tokenId}");

        $response->assertNoContent();

        // Verify token was deleted from database
        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $tokenId,
        ]);
    });

    it('prevents user from deleting another users token', function () {
        $otherUserToken = $this->otherUser->createToken('Other User Token', ['read']);
        $tokenId = $otherUserToken->accessToken->id;

        $response = login($this->user)->deleteJson("/v1/api-tokens/{$tokenId}");

        $response->assertNotFound()
            ->assertJson(['message' => 'Token not found']);

        // Verify token still exists in database
        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $tokenId,
        ]);
    });

    it('returns 404 when token does not exist', function () {
        $nonExistentTokenId = 99999;

        $response = login($this->user)->deleteJson("/v1/api-tokens/{$nonExistentTokenId}");

        $response->assertNotFound()
            ->assertJson(['message' => 'Token not found']);
    });

    it('prevents unauthenticated users from deleting tokens', function () {
        $token = $this->user->createToken('Test Token', ['read']);
        $tokenId = $token->accessToken->id;

        guest()->deleteJson("/v1/api-tokens/{$tokenId}")
            ->assertUnauthorized();

        // Verify token still exists
        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $tokenId,
        ]);
    });

    it('handles string token ID parameter', function () {
        $token = $this->user->createToken('Test Token', ['read']);
        $tokenId = (string) $token->accessToken->id;

        $response = login($this->user)->deleteJson("/v1/api-tokens/{$tokenId}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $tokenId,
        ]);
    });
});

describe('authorization edge cases', function () {
    it('prevents access to tokens belonging to different user types', function () {
        // Create a token manually with different tokenable_type
        $token = new PersonalAccessToken;
        $token->tokenable_id = $this->user->id;
        $token->tokenable_type = 'App\\Models\\SomeOtherModel'; // Different type
        $token->name = 'Test Token';
        $token->token = hash('sha256', 'test-token');
        $token->abilities = ['read'];
        $token->save();

        $response = login($this->user)->deleteJson("/v1/api-tokens/{$token->id}");

        $response->assertNotFound()
            ->assertJson(['message' => 'Token not found']);
    });

    it('ensures policy authorization is called for all actions', function () {
        // Test that accessing tokens requires proper authorization
        $user = User::factory()->create();

        // This should work for authenticated users
        login($user)->getJson('/v1/api-tokens')->assertOk();
        login($user)->postJson('/v1/api-tokens', [
            'token_name' => 'Test',
            'abilities' => ['read'],
        ])->assertOk();

        // Create and delete token
        $token = $user->createToken('Test', ['read']);
        login($user)->deleteJson("/v1/api-tokens/{$token->accessToken->id}")
            ->assertNoContent();
    });
});

describe('real world scenarios', function () {
    it('handles multiple tokens with same name', function () {
        // Users should be able to create multiple tokens with the same name
        login($this->user)->postJson('/v1/api-tokens', [
            'token_name' => 'API Token',
            'abilities' => ['read'],
        ])->assertOk();

        login($this->user)->postJson('/v1/api-tokens', [
            'token_name' => 'API Token',
            'abilities' => ['create'],
        ])->assertOk();

        $response = login($this->user)->getJson('/v1/api-tokens');
        $response->assertOk()
            ->assertJsonCount(2, 'data');
    });

    it('validates that abilities array cannot be empty', function () {
        // Empty abilities array should fail validation since 'required' rule treats empty arrays as missing
        $response = login($this->user)->postJson('/v1/api-tokens', [
            'token_name' => 'No Abilities Token',
            'abilities' => [],
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['abilities']);
    });

    it('allows creating tokens with special characters in name', function () {
        $specialName = 'API Token - 2024 (Production) #1';

        $response = login($this->user)->postJson('/v1/api-tokens', [
            'token_name' => $specialName,
            'abilities' => ['read'],
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $this->user->id,
            'name' => $specialName,
        ]);
    });

    it('maintains token security by not exposing token in index', function () {
        $this->user->createToken('Test Token', ['read']);

        $response = login($this->user)->getJson('/v1/api-tokens');

        $response->assertOk()
            ->assertJsonMissing(['token'])
            ->assertJsonMissing(['plain_text_token']);

        // Ensure the actual token value is not exposed
        $tokens = $response->json('data');
        foreach ($tokens as $token) {
            expect($token)->not->toHaveKey('token');
            expect($token)->not->toHaveKey('plain_text_token');
        }
    });
});

describe('token expiration', function () {
    it('creates token without expiration when expires is not provided', function () {
        $response = login($this->user)->postJson('/v1/api-tokens', [
            'token_name' => 'No Expiration Token',
            'abilities' => ['read'],
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $this->user->id,
            'name' => 'No Expiration Token',
            'expires_at' => null,
        ]);
    });

    it('creates token with 3 hour expiration', function () {
        $response = login($this->user)->postJson('/v1/api-tokens', [
            'token_name' => '3h Token',
            'abilities' => ['read'],
            'expires' => '3h',
        ]);

        $response->assertOk();

        // Check that token expires approximately 3 hours from now
        $token = $this->user->tokens()->where('name', '3h Token')->first();
        $expectedExpiry = now()->addHours(3);

        expect($token->expires_at)->not->toBeNull()
            ->and($token->expires_at->diffInMinutes($expectedExpiry))->toBeLessThan(2);
    });

    it('creates token with 24 hour expiration', function () {
        $response = login($this->user)->postJson('/v1/api-tokens', [
            'token_name' => '24h Token',
            'abilities' => ['read'],
            'expires' => '24h',
        ]);

        $response->assertOk();

        $token = $this->user->tokens()->where('name', '24h Token')->first();
        $expectedExpiry = now()->addHours(24);

        expect($token->expires_at)->not->toBeNull()
            ->and($token->expires_at->diffInMinutes($expectedExpiry))->toBeLessThan(2);
    });

    it('creates token with 3 day expiration', function () {
        $response = login($this->user)->postJson('/v1/api-tokens', [
            'token_name' => '3d Token',
            'abilities' => ['read'],
            'expires' => '3d',
        ]);

        $response->assertOk();

        $token = $this->user->tokens()->where('name', '3d Token')->first();
        $expectedExpiry = now()->addDays(3);

        expect($token->expires_at)->not->toBeNull()
            ->and($token->expires_at->diffInMinutes($expectedExpiry))->toBeLessThan(2);
    });

    it('creates token with 1 week expiration', function () {
        $response = login($this->user)->postJson('/v1/api-tokens', [
            'token_name' => '1w Token',
            'abilities' => ['read'],
            'expires' => '1w',
        ]);

        $response->assertOk();

        $token = $this->user->tokens()->where('name', '1w Token')->first();
        $expectedExpiry = now()->addWeek();

        expect($token->expires_at)->not->toBeNull()
            ->and($token->expires_at->diffInMinutes($expectedExpiry))->toBeLessThan(2);
    });

    it('creates token with 1 month expiration', function () {
        $response = login($this->user)->postJson('/v1/api-tokens', [
            'token_name' => '1m Token',
            'abilities' => ['read'],
            'expires' => '1m',
        ]);

        $response->assertOk();

        $token = $this->user->tokens()->where('name', '1m Token')->first();
        $expectedExpiry = now()->addMonth();

        expect($token->expires_at)->not->toBeNull()
            ->and($token->expires_at->diffInMinutes($expectedExpiry))->toBeLessThan(2);
    });

    it('creates token with never expiration', function () {
        $response = login($this->user)->postJson('/v1/api-tokens', [
            'token_name' => 'Never Expires Token',
            'abilities' => ['read'],
            'expires' => 'never',
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $this->user->id,
            'name' => 'Never Expires Token',
            'expires_at' => null,
        ]);
    });

    it('handles invalid expiration values by defaulting to no expiration', function () {
        $response = login($this->user)->postJson('/v1/api-tokens', [
            'token_name' => 'Invalid Expiration Token',
            'abilities' => ['read'],
            'expires' => 'invalid',
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $this->user->id,
            'name' => 'Invalid Expiration Token',
            'expires_at' => null,
        ]);
    });

    it('accepts expires as nullable in validation', function () {
        $response = login($this->user)->postJson('/v1/api-tokens', [
            'token_name' => 'Null Expiration Token',
            'abilities' => ['read'],
            'expires' => null,
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $this->user->id,
            'name' => 'Null Expiration Token',
            'expires_at' => null,
        ]);
    });

    it('validates expires as string when provided', function () {
        login($this->user)->postJson('/v1/api-tokens', [
            'token_name' => 'Test Token',
            'abilities' => ['read'],
            'expires' => 123, // Invalid: should be string or null
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['expires']);
    });

    it('creates token with complex abilities and expiration', function () {
        $response = login($this->user)->postJson('/v1/api-tokens', [
            'token_name' => 'Complex Token',
            'abilities' => ['read', 'create', 'update', 'delete'],
            'expires' => '1w',
        ]);

        $response->assertOk();

        $token = $this->user->tokens()->where('name', 'Complex Token')->first();
        $expectedExpiry = now()->addWeek();

        expect($token->abilities)->toBe(['read', 'create', 'update', 'delete'])
            ->and($token->expires_at)->not->toBeNull()
            ->and($token->expires_at->diffInMinutes($expectedExpiry))->toBeLessThan(2);
    });
});
