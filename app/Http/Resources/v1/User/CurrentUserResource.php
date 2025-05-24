<?php

declare(strict_types=1);

namespace App\Http\Resources\v1\User;

use App\Http\Resources\v1\LoadOrder\LoadOrderResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

/** @mixin \App\Models\User */
final class CurrentUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var User $resource */
        $resource = $this->resource;

        $email = Auth::user()?->is($resource) ? $this->email : (bool) $this->email;

        return [
            'name' => $this->name,
            'email' => $email,
            'verified' => $this->is_verified,
            'admin' => $this->isAdmin(),
            'profile' => $this->whenLoaded('profile', fn () => new UserProfileResource($this->profile)),
            'lists' => $this->whenLoaded('lists', fn () => LoadOrderResource::collection($this->lists)),
            'created' => $this->created_at,
            'updated' => $this->updated_at,
        ];
    }
}
