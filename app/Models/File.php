<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class File extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'clean_name', 'size_in_bytes'];

    protected $hidden = ['id'];

    public function lists(): BelongsToMany
    {
        return $this->belongsToMany('\App\Models\LoadOrder')->withTimestamps();
    }
}
