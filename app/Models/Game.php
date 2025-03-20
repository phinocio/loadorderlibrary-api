<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    /** @use HasFactory<\Database\Factories\GameFactory> */
    use HasFactory, Sluggable;

    protected $fillable = ['name', 'slug'];

    /**
     * Get the load orders for the game.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<LoadOrder>
     */
    public function lists(): HasMany
    {
        return $this->hasMany(LoadOrder::class);
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array<string, array<string, string>>
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }
}
