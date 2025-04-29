<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\v1\GameObserver;
use Carbon\CarbonInterface;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read string $slug
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
#[ObservedBy(GameObserver::class)]
final class Game extends Model
{
    /** @use HasFactory<\Database\Factories\GameFactory> */
    use HasFactory, Sluggable;

    /** @var list<string> */
    protected $fillable = ['name', 'slug'];

    // /** @return HasMany<LoadOrder, $this> */
    // public function lists(): HasMany
    // {
    //     return $this->hasMany(LoadOrder::class);
    // }

    /** @return array<string, array<string, string>> */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }
}
