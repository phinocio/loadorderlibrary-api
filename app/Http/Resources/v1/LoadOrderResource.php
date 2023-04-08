<?php

namespace App\Http\Resources\v1;

use App\Http\Resources\v1\AuthorResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoadOrderResource extends JsonResource
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
            'version' => $this->version,
            'slug' => $this->slug,
            'url' => config('app.main')."/v1/lists/$this->slug",
			'description' => $this->description,
			'website' => $this->website,
			'discord' => $this->discord,
			'readme' => $this->readme,
            'private' => (bool) $this->is_private,
            'expires' => $this->expires_at,
            'created' => $this->created_at,
            'updated' => $this->updated_at,
            'author' => new AuthorResource($this->author),
            'game' => new GameResource($this->game),
            'files' => FileResource::collection($this->files),
            'links' => [
                'url' => config('app.frontend_url')."/lists/$this->slug",
                'self' => config('app.url')."/v1/lists/$this->slug",
            ],
        ];
    }
}
