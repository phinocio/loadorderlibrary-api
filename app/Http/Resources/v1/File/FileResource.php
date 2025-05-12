<?php

declare(strict_types=1);

namespace App\Http\Resources\v1\File;

use App\Enums\v1\CacheKey;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/** @mixin File */
final class FileResource extends JsonResource
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
            'size_in_bytes' => $this->size_in_bytes,
            'content' => $this->formatFileContents(),
        ];
    }

    /**
     * Format the file contents.
     *
     * @return array<int, string>
     */
    private function formatFileContents(): array
    {
        /** @var string $content */
        $content = Cache::rememberForever(
            CacheKey::FILE->with($this->name, 'content'),
            function () {
                /** @var string|null $fileContent */
                $fileContent = Storage::disk('uploads')->get($this->name);

                // @codeCoverageIgnoreStart
                if ($fileContent === null) {
                    throw new RuntimeException("File {$this->name} not found in storage.");
                }
                // @codeCoverageIgnoreEnd

                return mb_trim($fileContent);
            }
        );

        if ($this->clean_name === 'modlist.txt') {
            return array_reverse(explode("\n", $content));
        }

        return explode("\n", $content);
    }
}
