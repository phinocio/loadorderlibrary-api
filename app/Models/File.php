<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\v1\FileObserver;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read string $clean_name
 * @property-read int $size_in_bytes
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 * @property-read LoadOrder[] $lists
 */
#[ObservedBy(FileObserver::class)]
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

    /** @return BelongsToMany<LoadOrder, $this> */
    public function lists(): BelongsToMany
    {
        return $this->belongsToMany(LoadOrder::class)->withTimestamps();
    }
}
