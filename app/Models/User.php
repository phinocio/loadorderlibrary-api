<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
		'two_factor_secret'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
		'created_at' => 'timestamp'
    ];

	public function lists()
	{
		return $this->hasMany('\App\Models\LoadOrder');
	}

	public function isAdmin()
	{
		return $this->is_admin === 1;
	}

	// public function enabledTwoFactor()
	// {
	// 	return $this->two_factor_secret !== null;
	// }
}
