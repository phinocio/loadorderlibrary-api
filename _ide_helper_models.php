<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $clean_name
 * @property int $size_in_bytes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoadOrder> $lists
 * @property-read int|null $lists_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereCleanName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereSizeInBytes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereUpdatedAt($value)
 */
	class File extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoadOrder> $lists
 * @property-read int|null $lists_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 */
	class Game extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $user_id
 * @property int $game_id
 * @property string $name
 * @property string $slug
 * @property string|null $version
 * @property string|null $description
 * @property string|null $verison
 * @property string|null $discord
 * @property string|null $readme
 * @property string|null $website
 * @property bool $is_private
 * @property string|null $expires_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadOrder onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadOrder whereDeletedAt($value)
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadOrder whereVerison($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadOrder whereVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadOrder whereWebsite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadOrder withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadOrder withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadOrder withoutTrashed()
 */
	class LoadOrder extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property bool $is_verified
 * @property bool $is_admin
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

