<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\FileStatsResource;
use App\Http\Resources\v1\LoadOrderStatsResource;
use App\Http\Resources\v1\StatsResource;
use App\Http\Resources\v1\UserStatsResource;
use App\Models\File;
use App\Models\LoadOrder;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class StatsController extends Controller
{
    /**
     * For the all stats route, which will I assume be hit the most,
     * we cache it for 15 minutes in production since it has proven to
     * be pretty intensive in the past, and instant statistics is not
     * really needed.
     */
    public function index(): JsonResponse
    {
        $stats = Cache::get('stats', null);

        if (! $stats) {
            $stats = json_encode(new StatsResource([
                'users' => User::select(['id', 'is_verified', 'is_admin', 'email', 'created_at'])->with('lists:id,user_id')->latest()->get(),
                'files' => File::with('lists:id')->get(),
                'lists' => LoadOrder::select(['id', 'is_private', 'user_id'])->latest()->get(),
            ]));

            Cache::set('stats', $stats, 900);
        }

        return response()->json(json_decode($stats));
    }

    /*
     * This works perfectly fine and makes sense imo. No real need for separate
     * controllers or methods for each individual stat resource when the Services
     * do the heavy work.
     */
    public function show(string $resource): UserStatsResource|FileStatsResource|LoadOrderStatsResource|JsonResponse
    {
        return match ($resource) {
            'users' => new UserStatsResource(User::select([
                'id', 'is_verified', 'is_admin', 'email', 'created_at',
            ])->with('lists:id,user_id')->latest()->get()),
            'files' => new FileStatsResource(File::with('lists:id')->latest()->get()),
            'lists' => new LoadOrderStatsResource(LoadOrder::select(['id', 'is_private', 'user_id'])->latest()->get()),
            default => response()->json(['message' => 'No stats exist for this resource.'], 404),
        };
    }
}
