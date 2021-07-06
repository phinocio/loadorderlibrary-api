<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
			'name' => $this->name,
			'clean_name' => $this->clean_name,
			'bytes' => $this->size_in_bytes,
			'created' => $this->created_at,
			'updated' => $this->updated_at
		];
    }
}
