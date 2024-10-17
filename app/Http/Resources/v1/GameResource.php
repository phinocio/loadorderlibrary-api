<?php

namespace App\Http\Resources\v1;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Game */
class GameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'lists' => LoadOrderResource::collection($this->whenLoaded('loadOrders')),
            'lists_count' => $this->whenNotNull($this->load_orders_count ?? null),
        ];
    }
}
