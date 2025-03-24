<?php

declare(strict_types=1);

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Game extends Model
{
    use Sluggable;

    /** @var list<string> */
    protected $fillable = ['name', 'slug'];

    /** @return HasMany<LoadOrder, Game> */
    public function lists(): HasMany
    {
        return $this->hasMany(LoadOrder::class);
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
