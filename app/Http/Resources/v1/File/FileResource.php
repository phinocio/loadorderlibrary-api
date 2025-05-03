<?php

declare(strict_types=1);

namespace App\Http\Resources\v1\File;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin File */
final class FileResource extends JsonResource
{
    /**
     * Create a new resource instance.
     *
     * @param  array<int, string>  $content
     */
    public function __construct(File $resource, public ?array $content = null)
    {
        parent::__construct($resource);
    }

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
            'size_in_bytes' => $this->size_in_bytes,
            'content' => $this->content,
            // 'lists' => FileListResource::collection($this->whenLoaded('lists')),
        ];
    }
}
