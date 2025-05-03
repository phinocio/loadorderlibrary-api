<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class File extends Model
{
    /** @use HasFactory<\Database\Factories\FileFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'clean_name',
        'size_in_bytes',
    ];

    // /** @return BelongsToMany<LoadOrder, $this> */
    // public function lists(): BelongsToMany
    // {
    //     return $this->belongsToMany(LoadOrder::class)->withTimestamps();
    // }
}
