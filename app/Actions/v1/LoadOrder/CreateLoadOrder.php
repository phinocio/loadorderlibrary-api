<?php

declare(strict_types=1);

namespace App\Actions\v1\LoadOrder;

use App\Actions\v1\File\UploadFile;
use App\Models\LoadOrder;
use Carbon\CarbonImmutable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
     *     private?: bool,
     *     expires?: ?string,
     *     game: int,
     *     files: array<UploadedFile>
     * } $data
     */
    public function execute(array $data): LoadOrder
    {
        return DB::transaction(function () use ($data) {
            $loadOrder = LoadOrder::query()->create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'version' => $data['version'] ?? null,
                'website' => $data['website'] ?? null,
                'discord' => $data['discord'] ?? null,
                'readme' => $data['readme'] ?? null,
                'is_private' => $data['private'] ?? false,
                'expires_at' => $this->calculateExpiration($data['expires'] ?? null),
                'game_id' => $data['game'],
                'user_id' => Auth::id(),
            ]);

            foreach ($data['files'] as $uploadedFile) {
                $file = $this->uploadFile->execute($uploadedFile);
                $loadOrder->files()->attach($file);
            }

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
