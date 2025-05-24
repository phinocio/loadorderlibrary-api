<?php

declare(strict_types=1);

namespace App\Http\Resources\v1\LoadOrder;

use App\Http\Resources\v1\File\FileResource;
use App\Http\Resources\v1\Game\GameResource;
use App\Http\Resources\v1\User\AuthorResource;
use App\Models\LoadOrder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin LoadOrder */
final class LoadOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $appUrl = is_string(config('app.url')) ? config('app.url') : '';

        return [
            'name' => $this->name,
            'version' => $this->version,
            'slug' => $this->slug,
            'url' => "{$appUrl}/v1/lists/{$this->slug}",
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
            'files' => FileResource::collection($this->whenLoaded('files')),
            'links' => [
                'url' => "/lists/{$this->slug}",
                'self' => "{$appUrl}/v1/lists/{$this->slug}",
            ],
        ];
    }
}
