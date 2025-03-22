<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class File extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'clean_name',
        'size_in_bytes',
    ];

    /**
     * Get the load orders that the file is part of.
     *
     * @return BelongsToMany<LoadOrder>
     */
    public function lists(): BelongsToMany
    {
        return $this->belongsToMany(LoadOrder::class)->withTimestamps();
    }
}
