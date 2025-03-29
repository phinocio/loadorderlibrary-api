<?php

declare(strict_types=1);

namespace App\Http\Resources\v1\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

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
        $isSelf = Auth::user()?->is($this->resource);
        $isAdmin = Auth::user()?->is_admin;

        $email = $isSelf ? $this->email : (bool) $this->email;

        return [
            'name' => $this->name,
            'email' => $this->when($isSelf || $isAdmin, $email),
            'verified' => $this->is_verified,
            'admin' => $this->is_admin,
            'profile' => $this->whenLoaded('profile', fn () => new UserProfileResource($this->profile)),
            'created' => $this->created_at,
            'updated' => $this->updated_at,
            'links' => [
                'url' => route('users.show', $this->name, false),
                'self' => route('users.show', $this->name),
            ],
        ];
    }
}
