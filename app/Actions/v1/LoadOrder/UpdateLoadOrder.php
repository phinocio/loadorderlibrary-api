<?php

declare(strict_types=1);

namespace App\Actions\v1\LoadOrder;

use App\Actions\v1\File\UploadFile;
use App\Models\LoadOrder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use RuntimeException;

final class UpdateLoadOrder
{
    public function __construct(
        private readonly UploadFile $uploadFile,
    ) {}

    /** @param array{
     *     name?: string,
     *     description?: ?string,
     *     version?: ?string,
     *     website?: ?string,
     *     discord?: ?string,
     *     readme?: ?string,
     *     is_private?: bool,
     *     expires_at?: ?string,
     *     game_id?: int,
     *     files?: array<UploadedFile>
     * } $data
     */
    public function execute(LoadOrder $loadOrder, array $data): LoadOrder
    {
        return DB::transaction(function () use ($loadOrder, $data) {
            // Update data except files
            $files = $data['files'] ?? null;
            unset($data['files']);
            $loadOrder->update($data);

            if ($files) {
                $loadOrder->files()->detach();
                foreach ($files as $uploadedFile) {
                    $file = $this->uploadFile->execute($uploadedFile);
                    $loadOrder->files()->attach($file);
                }
            }

            $refreshedLoadOrder = $loadOrder->load(['author', 'game'])->fresh();

            if ($refreshedLoadOrder === null) {
                throw new RuntimeException('Load order was deleted during update');
            }

            return $refreshedLoadOrder;
        });
    }
}
