<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function loadOrders(): HasMany
    {
        return $this->hasMany('\App\Models\LoadOrder');
    }
}
