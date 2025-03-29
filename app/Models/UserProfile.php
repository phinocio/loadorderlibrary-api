<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
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
