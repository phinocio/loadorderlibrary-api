<?php

declare(strict_types=1);

namespace App\Http\Resources\v1\User;

use App\Http\Resources\v1\LoadOrder\LoadOrderResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\User */
final class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isAdminRoute = $request->routeIs('admin.*');

        return [
            'name' => $this->name,
            'verified' => $this->is_verified,
            'admin' => $this->when($isAdminRoute, fn () => $this->isAdmin()),
            'profile' => $this->whenLoaded('profile', fn () => new UserProfileResource($this->profile)),
            'lists' => $this->whenLoaded('publicLists', fn () => LoadOrderResource::collection($this->publicLists)),
            'created' => $this->created_at,
            'updated' => $this->updated_at,
        ];
    }
}
