<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\FileStatsResource;
use App\Http\Resources\v1\StatsResource;
use App\Http\Resources\v1\UserStatsResource;
use App\Models\File;
use App\Models\User;

class StatsController extends Controller
{
    // TODO: Probably find a way to cache responses for like 15m because they are quite database intensive
    public function index()
    {
        return new StatsResource([
            'users' => User::select(['id', 'is_verified', 'is_admin', 'email'])->with('lists:id,user_id')->latest()->get(),
            'files' => File::with('lists:id')->get(),
        ]);
    }

    /*
     * This works perfectly fine and makes sense imo. No real need for separate
     * controllers or methods for each individual stat resource when the Services
     * do the heavy work.
     */
    public function show(string $resource)
    {
        return match ($resource) {
            'users' => new UserStatsResource(User::select([
                'id', 'is_verified', 'is_admin', 'email',
            ])->with('lists:id,user_id')->latest()->get()),
            'files' => new FileStatsResource(File::with('lists:id')->latest()->get()),
            default => response()->json(['message' => 'No stats exist for this resource.'], 404),
        };
    }
}
