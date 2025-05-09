<?php

declare(strict_types=1);

namespace App\Actions\v1\LoadOrder;

use App\Actions\v1\File\UploadFile;
use App\Models\LoadOrder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

final class CreateLoadOrder
{
    public function __construct(
        private readonly UploadFile $uploadFile,
    ) {}

    /** @param array{
     *     name: string,
     *     description?: ?string,
     *     version?: ?string,
     *     website?: ?string,
     *     discord?: ?string,
     *     readme?: ?string,
     *     is_private?: bool,
     *     expires_at?: ?string,
     *     game_id: int,
     *     files: array<UploadedFile>
     * } $data
     */
    public function execute(array $data): LoadOrder
    {
        $loadOrder = LoadOrder::query()->create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'version' => $data['version'] ?? null,
            'website' => $data['website'] ?? null,
            'discord' => $data['discord'] ?? null,
            'readme' => $data['readme'] ?? null,
            'is_private' => $data['is_private'] ?? false,
            'expires_at' => $data['expires_at'] ?? null,
            'user_id' => Auth::user()->id ?? null,
            'game_id' => $data['game_id'],
        ]);

        foreach ($data['files'] as $uploadedFile) {
            $file = $this->uploadFile->execute($uploadedFile);
            $loadOrder->files()->attach($file);
        }

        return $loadOrder->load(['files', 'author', 'game']);
    }
}
