<?php

namespace App\Http\Resources\v1;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin(User)
 */
class UserStatsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'total' => $this->count(),
            'without_email' => count($this->resource->filter(function ($value) {
                return $value->email === null;
            })),
            'verified_authors' => count($this->resource->filter(function ($value) {
                return $value->is_verified;
            })),
            'with_lists' => count($this->resource->filter(function ($value) {
                return count($value->lists);
            })),
            'last_registered' => Carbon::createFromDate($this->resource[0]->created_at)->diffForHumans(),
            'links' => [
                'self' => route('stats.show', 'users'),
            ],
        ];
    }
}
