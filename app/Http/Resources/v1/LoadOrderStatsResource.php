<?php

namespace App\Http\Resources\v1;

use App\Models\LoadOrder;
use Carbon\Carbon;
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
                return $value->is_private;
            })),
            'anonymous_lists' => count($this->resource->filter(function ($value) {
                return $value->user_id === null;
            })),
            'last_created' => Carbon::createFromDate($this->resource[0]->created_at)->diffForHumans(),
            'links' => [
                'self' => route('stats.show', 'lists.index'),
            ],
        ];
    }
}
