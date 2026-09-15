<?php

declare(strict_types=1);

namespace App\Http\Resources\v1\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\UserProfile
 */
final class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'avatar' => $this->avatar,
            'bio' => $this->bio ?? '',
            'discord' => $this->discord ?? '',
            'kofi' => $this->kofi ?? '',
            'patreon' => $this->patreon ?? '',
            'website' => $this->website ?? '',
        ];
    }
}
