<?php

namespace App\Http\Resources\v1;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/** @mixin File */
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
            'content' => $this->when(! $request->routeIs('compare.*'), $this->formatFileContents()),
            'created' => $this->created_at,
            'updated' => $this->updated_at,
        ];
    }

    /**
     * @return array<string>
     *
     * This makes the most sense to do on the server, I think. This also makes JavaScript slightly less required
     * on the frontend.
     */
    private function formatFileContents(): array
    {
        $content = trim(Storage::disk('uploads')->get($this->name));

        if ($this->clean_name === 'modlist.txt') {
            return array_reverse(explode("\n", $content));
        } else {
            return explode('\n', $content);
        }
    }
}
