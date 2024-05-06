<?php

namespace App\Http\Resources\v1;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin(File) */
class FileStatsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $tmpFiles = \Storage::disk('tmp')->allFiles();
        $tmpSize = 0;

        foreach ($tmpFiles as $file) {
            $tmpSize += \Storage::disk('tmp')->size($file);
        }

        return [
            'total' => $this->count(),
            'total_size_in_bytes' => $this->sum('size_in_bytes'),
            'total_tmp_size_in_bytes' => $tmpSize
        ];
    }
}
