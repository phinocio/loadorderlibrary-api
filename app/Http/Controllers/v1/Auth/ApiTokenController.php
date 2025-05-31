<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Auth;

use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\Auth\StoreApiTokenRequest;
use App\Http\Resources\v1\Auth\ApiTokenResource;
use App\Policies\v1\ApiTokenPolicy;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Laravel\Sanctum\PersonalAccessToken;

final class ApiTokenController extends ApiController
{
    protected string $policyClass = ApiTokenPolicy::class;

    public function index(): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', PersonalAccessToken::class);

        // Return the user's existing tokens
        $user = Auth::user();

        return ApiTokenResource::collection($user?->tokens()->latest()->get());
    }

    public function store(StoreApiTokenRequest $request): JsonResponse
    {
        Gate::authorize('create', PersonalAccessToken::class);

        /** @var array{token_name: string, abilities: array<string>, expires?: string} */
        $data = $request->validated();

        $user = Auth::user();

        $expiresAt = null;
        if (isset($data['expires'])) {
            $expiresAt = $this->calculateExpiration($data['expires']);
        }

        $token = $user?->createToken($data['token_name'], $data['abilities'], $expiresAt)->plainTextToken;

        return response()->json([
            'data' => [
                'token' => $token,
            ],
        ]);
    }

    public function destroy(string $token): JsonResponse
    {
        $user = Auth::user();
        /** @var PersonalAccessToken|null $personalAccessToken */
        $personalAccessToken = $user?->tokens()->where('id', $token)->first();

        if (! $personalAccessToken) {
            return response()->json(['message' => 'Token not found'], 404);
        }

        Gate::authorize('delete', $personalAccessToken);

        $personalAccessToken->delete();

        return response()->json(null, 204);
    }

    private function calculateExpiration(string $expires): ?CarbonImmutable
    {
        return match ($expires) {
            '3h' => CarbonImmutable::now()->addHours(3),
            '24h' => CarbonImmutable::now()->addHours(24),
            '3d' => CarbonImmutable::now()->addDays(3),
            '1w' => CarbonImmutable::now()->addWeek(),
            '1m' => CarbonImmutable::now()->addMonth(),
            'never' => null,
            default => Auth::check() ? null : CarbonImmutable::now()->addHours(24),
        };
    }
}
