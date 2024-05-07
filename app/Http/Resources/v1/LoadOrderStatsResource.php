<?php

namespace App\Http\Resources\v1;

use App\Models\LoadOrder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin(LoadOrder) */
class LoadOrderStatsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // The total is used in some calcs for percentages, so
        // just get it here instead of needing to call ->count()
        // multiple times.
        $total = $this->count();

        return [
            'total' => $this->count(),
            'private_lists' => count($this->resource->filter(function ($value) {
                return $value->is_private === 1;
            })),
            'anonymous_lists' => count($this->resource->filter(function ($value) {
                return $value->user_id === null;
            })),
            'links' => [
                'self' => route('stats.show', 'lists'),
            ],
        ];
    }
}
