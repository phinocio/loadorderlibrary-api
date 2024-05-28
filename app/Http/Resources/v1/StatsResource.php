<?php

namespace App\Http\Resources\v1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatsResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Manually wrap in data so it plays nicely with statscontroller json_encode for caching *just* the json.
        return [
            'data' => [
                'files' => new FileStatsResource($this->resource['files']),
                'lists' => new LoadOrderStatsResource($this->resource['lists']),
                'users' => new UserStatsResource($this->resource['users']),
            ],
            'links' => [
                'self' => route('stats.index'),
            ],
            'meta' => [
                'last_updated' => Carbon::now(),
            ],
        ];
    }
}
