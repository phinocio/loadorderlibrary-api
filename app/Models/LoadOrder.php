<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoadOrder extends Model
{
    use HasFactory;

	protected $guarded = [];
	protected $with = ['files', 'game'];
	protected $hidden = ['id'];

	public function game()
	{
		return $this->belongsTo('\App\Models\Game');
	}

	public function author()
	{
		return $this->belongsTo('\App\Models\User', 'user_id');
	}

	public function files()
	{
		return $this->belongsToMany('\App\Models\File')->withTimestamps();
	}
}
