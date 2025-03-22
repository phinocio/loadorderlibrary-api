<?php

declare(strict_types=1);

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoadOrder extends Model
{
    /** @use HasFactory<\Database\Factories\LoadOrderFactory> */
    use HasFactory, Sluggable, SoftDeletes;

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
    ];

    /** @return BelongsTo<User, LoadOrder> */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Game, LoadOrder> */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    /** @return BelongsToMany<File, LoadOrder> */
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
}
