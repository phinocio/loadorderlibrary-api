<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 *
 *
 * @property int $id
 * @property int|null $user_id
 * @property int $game_id
 * @property string $slug
 * @property string $name
 * @property string|null $description
 * @property string|null $version
 * @property string|null $website
 * @property string|null $readme
 * @property string|null $discord
 * @property bool $is_private
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $author
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\File> $files
 * @property-read int|null $files_count
 * @property-read \App\Models\Game $game
 * @method static \Database\Factories\LoadOrderFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadOrder findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadOrder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadOrder whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadOrder whereDiscord($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadOrder whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadOrder whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadOrder whereIsPrivate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadOrder whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadOrder whereReadme($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadOrder whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadOrder whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadOrder whereVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadOrder whereWebsite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadOrder withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 * @mixin \Eloquent
 */
class LoadOrder extends Model
{
    use HasFactory;
    use Sluggable;

    protected $hidden = ['id'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function files(): BelongsToMany
    {
        return $this->belongsToMany(File::class)->withTimestamps();
    }

    /**
     * Return the sluggable configuration array for this model.
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
