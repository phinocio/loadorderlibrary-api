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
            'content' => $this->formatFileContents(),
            'created' => $this->created_at,
            'updated' => $this->updated_at,
        ];
    }

    /**

     * @return array<string>
     *
     * This makes the most sense to do on the server, I think. This also makes JavaScript slightly less required
     * on the frontend.
     *
     */
    private function formatFileContents(): array
    {
        $content = trim(Storage::disk('uploads')->get($this->name));
        // modlist.txt itself is in "reverse" order, so we need to reverse that to get it into
        // the expected order for a human to read.
        if ($this->clean_name === "modlist.txt") {
            $content = array_reverse(explode("\n", $content)) ;
        }

        if ($this->clean_name === "plugins.txt") {
            $content = explode("\n", preg_replace("/[*]/", "", $content));
            array_splice($content, 0, 1);
        }

        return $content;
    }
}
