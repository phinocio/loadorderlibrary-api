<?php

declare(strict_types=1);

namespace App\Http\Resources\v1;

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
        $email = Auth::user()?->is($this->resource) ? $this->email : (bool) $this->email;

        return [
            'name' => $this->name,
            'email' => $email,
            'verified' => $this->is_verified,
            'admin' => $this->is_admin,
            'bio' => $this->bio,
            'discord' => $this->discord,
            'kofi' => $this->kofi,
            'patreon' => $this->patreon,
            'website' => $this->website,
            'created' => $this->created_at,
            'updated' => $this->updated_at,
            'links' => [
                'url' => route('users.show', $this->name, false),
                'self' => route('users.show', $this->name),
            ],
        ];
    }
}
