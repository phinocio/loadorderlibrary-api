<?php

declare(strict_types=1);

namespace App\Http\Resources\v1\Game;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Game */
final class GameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
        ];

        if ($request->routeIs('games.*', 'admin.games.*')) {
            $data['lists_count'] = $this->lists_count;
        }

        return $data;
    }
}
