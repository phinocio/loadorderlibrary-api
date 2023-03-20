<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LoadOrder extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['files', 'game', 'author'];

    protected $hidden = ['id'];

    protected $casts = [
        'expires_at' => 'datetime'
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo('\App\Models\Game');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo('\App\Models\User', 'user_id');
    }

    public function files(): BelongsToMany
    {
        return $this->belongsToMany('\App\Models\File')->withTimestamps();
    }
}
