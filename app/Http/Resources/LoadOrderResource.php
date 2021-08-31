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
			'version' => $this->version,
			'slug' => $this->slug,
			'url' => config('app.main') . "/lists/$this->slug",
			'private' => (bool) $this->is_private,
			'expires' => $this->expires_at,
			'created' => $this->created_at,
			'updated' => $this->updated_at,
			'author' => new UserResource($this->author),
			'game' => new GameResource($this->game),
			'files' => FileResource::collection($this->files),
			'links' => [
				'url' => config('app.main') . "/lists/$this->slug",
				'self' => config('app.url') . "/v1/lists/$this->slug"
			]
		];
    }
}
