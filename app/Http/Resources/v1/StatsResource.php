<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
}
