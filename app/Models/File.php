<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
	use HasFactory;

	protected $fillable = ['name', 'clean_name', 'size_in_bytes'];
	protected $hidden = ['id'];

	protected $casts = [
		'created_at' => 'timestamp',
		'updated_at' => 'timestamp'
	];

	public function lists()
	{
		return $this->belongsToMany('\App\Models\LoadOrder')->withTimestamps();
	}
}
