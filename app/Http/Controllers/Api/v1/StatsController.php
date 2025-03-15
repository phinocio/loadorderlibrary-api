<?php

namespace App\Http\Controllers\Api\v1;

use App\Enums\CacheTag;
use App\Http\Resources\v1\FileStatsResource;
use App\Http\Resources\v1\LoadOrderStatsResource;
use App\Http\Resources\v1\StatsResource;
use App\Http\Resources\v1\UserStatsResource;
use App\Models\File;
use App\Models\LoadOrder;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class StatsController extends ApiController
{
    /**
     * For the all stats route, which will I assume be hit the most,
     * we cache it for 15 minutes in production since it has proven to
     * be pretty intensive in the past, and instant statistics is not
     * really needed.
     */
    public function index(): JsonResponse
    {
        $stats = Cache::tags([CacheTag::STATS->value])->flexible('stats', [600, 900], function () {
            return json_encode(new StatsResource([
                'users' => User::query()->select(['id', 'is_verified', 'is_admin', 'email', 'created_at'])->with('lists:id,user_id')->latest()->get(),
                'files' => File::with('lists:id')->get(),
                'lists' => LoadOrder::query()->select(['id', 'is_private', 'user_id', 'created_at'])->latest()->get(),
            ]));
        });

        return response()->json(json_decode((string) $stats));
    }

    /*
     * This works perfectly fine and makes sense imo. No real need for separate
     * controllers or methods for each individual stat resource when the Services
     * do the heavy work.
     */
    public function show(string $resource): UserStatsResource|FileStatsResource|LoadOrderStatsResource|JsonResponse
    {
        return match ($resource) {
            'users' => new UserStatsResource(User::query()->select([
                'id', 'is_verified', 'is_admin', 'email', 'created_at',
            ])->with('lists:id,user_id')->latest()->get()),
            'files' => new FileStatsResource(File::with('lists:id')->latest()->get()),
            'lists' => new LoadOrderStatsResource(LoadOrder::query()->select(['id', 'is_private', 'user_id', 'created_at'])->latest()->get()),
            default => response()->json(['message' => 'No stats exist for this resource.'], 404),
        };
    }
}
