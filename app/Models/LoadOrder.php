<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\v1\LoadOrderObserver;
use Carbon\CarbonInterface;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read string $slug
 * @property-read string|null $description
 * @property-read string|null $version
 * @property-read string|null $website
 * @property-read string|null $discord
 * @property-read string|null $readme
 * @property-read bool $is_private
 * @property-read CarbonInterface|null $expires_at
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 * @property-read User $author
 * @property-read Game $game
 * @property-read File[] $files
 */
#[ObservedBy(LoadOrderObserver::class)]
final class LoadOrder extends Model
{
    /** @use HasFactory<\Database\Factories\LoadOrderFactory> */
    use HasFactory, Sluggable;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'version',
        'website',
        'discord',
        'readme',
        'is_private',
        'expires_at',
        'user_id',
        'game_id',
    ];

    /** @return BelongsTo<User, $this> */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** @return BelongsTo<Game, $this> */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    /** @return BelongsToMany<File, $this> */
    public function files(): BelongsToMany
    {
        return $this->belongsToMany(File::class)->withTimestamps();
    }

    /** @return array<string, array<string, string>> */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'is_private' => 'boolean',
        ];
    }

    /**
     * Scope a query to only include non-expired load orders.
     *
     * @param  Builder<LoadOrder>  $query
     */
    #[Scope]
    protected function expired(Builder $query): void
    {
        $query->where('expires_at', '<=', now());
    }
}
