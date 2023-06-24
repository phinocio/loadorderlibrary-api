<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
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
            'clean_name' => $this->clean_name,
            'bytes' => $this->size_in_bytes,
            'created' => $this->created_at,
            'updated' => $this->updated_at,
        ];
    }
}
