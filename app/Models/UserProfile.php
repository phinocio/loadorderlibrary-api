<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\v1\UserProfileObserver;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property-read int $user_id
 * @property-read string|null $bio
 * @property-read string|null $discord
 * @property-read string|null $kofi
 * @property-read string|null $patreon
 * @property-read string|null $website
 * @property-read CarbonInterface|null $created_at
 * @property-read CarbonInterface|null $updated_at
 * @property-read User $user
 */
#[ObservedBy(UserProfileObserver::class)]
final class UserProfile extends Model
{
    /** @use HasFactory<\Database\Factories\UserProfileFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'bio',
        'discord',
        'kofi',
        'patreon',
        'website',
    ];

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
