<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class File extends Model
{
    /** @use HasFactory<\Database\Factories\FileFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'clean_name',
        'size_in_bytes',
    ];

    /**
     * Get the load orders that the file is part of.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<LoadOrder>
     */
    public function lists(): BelongsToMany
    {
        return $this->belongsToMany(LoadOrder::class)->withTimestamps();
    }
}
