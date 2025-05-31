<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\v1\UserObserver;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read string|null $email
 * @property-read CarbonInterface|null $email_verified_at
 * @property-read string $password
 * @property-read string|null $remember_token
 * @property-read bool $is_verified
 * @property-read bool $is_admin
 * @property-read string|null $verification_token
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 * @property-read UserProfile|null $profile
 * @property-read LoadOrder[] $lists
 */
#[ObservedBy(UserObserver::class)]
final class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_verified',
    ];

    /** @var list<string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    /** @return HasOne<UserProfile, $this> */
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /** @return HasMany<LoadOrder, $this> */
    public function lists(): HasMany
    {
        return $this->hasMany(LoadOrder::class);
    }

    /** @return HasMany<LoadOrder, $this> */
    public function publicLists(): HasMany
    {
        return $this->hasMany(LoadOrder::class)->where('is_private', false);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
            'is_admin' => 'boolean',
        ];
    }
}
