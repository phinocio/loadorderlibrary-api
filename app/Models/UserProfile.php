<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\v1\UserProfileObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    /** @return BelongsTo<User, UserProfile> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
