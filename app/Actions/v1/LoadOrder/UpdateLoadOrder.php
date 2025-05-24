<?php

declare(strict_types=1);

namespace App\Actions\v1\LoadOrder;

use App\Actions\v1\File\UploadFile;
use App\Models\LoadOrder;
use Carbon\CarbonImmutable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
     *     private?: bool,
     *     expires?: ?string,
     *     game?: int,
     *     files?: array<UploadedFile>
     * } $data
     */
    public function execute(LoadOrder $loadOrder, array $data): LoadOrder
    {
        return DB::transaction(function () use ($loadOrder, $data) {
            $files = $data['files'] ?? null;

            if ($files) {
                unset($data['files']);
                $loadOrder->files()->detach();
                foreach ($files as $uploadedFile) {
                    $file = $this->uploadFile->execute($uploadedFile);
                    $loadOrder->files()->attach($file);
                }
            }

            if (isset($data['expires'])) {
                $data['expires_at'] = $this->calculateExpiration($data['expires']);
                unset($data['expires']);
            }

            if (isset($data['private'])) {
                $data['is_private'] = $data['private'];
                unset($data['private']);
            }

            if (isset($data['game'])) {
                $data['game_id'] = $data['game'];
                unset($data['game']);
            }

            $loadOrder->update($data);

            return $loadOrder->load(['files']);
        });
    }

    private function calculateExpiration(?string $expires): ?CarbonImmutable
    {
        if (! $expires) {
            return Auth::check() ? null : CarbonImmutable::now()->addHours(24);
        }

        return match ($expires) {
            '3h' => CarbonImmutable::now()->addHours(3),
            '24h' => CarbonImmutable::now()->addHours(24),
            '3d' => CarbonImmutable::now()->addDays(3),
            '1w' => CarbonImmutable::now()->addWeek(),
            '1m' => CarbonImmutable::now()->addMonth(),
            'never' => null,
            default => Auth::check() ? null : now()->addHours(24),
        };
    }
}
