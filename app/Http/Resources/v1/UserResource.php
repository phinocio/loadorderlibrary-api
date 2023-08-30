<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'verified' => (bool) $this->is_verified,
            'admin' => (bool) $this->is_admin,
            'created' => $this->created_at,
            'updated' => $this->updated_at,
        ];
    }
}
