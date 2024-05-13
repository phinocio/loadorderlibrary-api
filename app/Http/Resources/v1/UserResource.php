<?php

namespace App\Http\Resources\v1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin(User) */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'email' => $request->routeIs('admin.user.*') ? (bool) $this->email : $this->email, // I don't need to see people's actual emails
            'verified' => (bool) $this->is_verified,
            'admin' => (bool) $this->is_admin,
            'created' => $this->created_at,
            'updated' => $this->updated_at,
            'lists' => LoadOrderResource::collection($this->whenLoaded('lists')),
        ];
    }
}
