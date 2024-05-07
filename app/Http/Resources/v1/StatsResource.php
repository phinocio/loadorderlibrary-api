<?php

namespace App\Http\Resources\v1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;

class StatsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'files' => new FileStatsResource($this->resource['files']),
            'lists' => new LoadOrderStatsResource($this->resource['lists']),
            'users' => new UserStatsResource($this->resource['users']),
        ];
    }

    public function with(Request $request): array
    {
       return [
           'links' => [
               'self' => route('stats.index'),
           ],
           'meta' => [
               'last_updated' => Cache::remember('stats-updated', 900, function () {
                   return Carbon::now();
               }),
           ],

       ];
    }
}
