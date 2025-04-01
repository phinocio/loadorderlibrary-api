<?php

declare(strict_types=1);

namespace App\Http\Resources\v1\User;

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
        return [
            'name' => $this->name,
            'verified' => $this->is_verified,
            'profile' => $this->whenLoaded('profile', fn () => new UserProfileResource($this->profile)),
            'created' => $this->created_at,
            'updated' => $this->updated_at,
            'links' => [
                'url' => route('users.profile.show', $this->name, false),
                'self' => route('users.profile.show', $this->name),
            ],
        ];
    }
}
