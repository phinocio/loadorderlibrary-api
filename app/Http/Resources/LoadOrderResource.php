<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoadOrderResource extends JsonResource
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
			'slug' => $this->slug,
			'private' => $this->is_private,
			'created' => $this->created_at,
			'updated' => $this->updated_at,
			'author' => new UserResource($this->author),
			'game' => new GameResource($this->game),
			'files' => FileResource::collection($this->files)
		];
    }
}
