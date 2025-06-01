<?php

declare(strict_types=1);

namespace App\Http\Resources\v1\Auth;

use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read array<string> $abilities
 * @property-read CarbonInterface|null $last_used_at
 * @property-read CarbonInterface|null $expires_at
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
final class ApiTokenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'abilities' => $this->abilities,
            'last_used' => $this->last_used_at,
            'expires' => $this->expires_at,
            'created' => $this->created_at,
            'updated' => $this->updated_at,
        ];
    }
}
