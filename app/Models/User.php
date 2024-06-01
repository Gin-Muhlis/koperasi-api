<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable {
	use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */protected $fillable = [
		'username',
		'email',
		'password',
		'uuid',
		'member_id',
		'active',
		'last_login_ip',
		'last_login_time',
	];

	protected $guard_name = 'api';

	protected $searchableFields = ['*'];

	protected $hidden = ['password', 'remember_token'];

	protected $casts = [
		'email_verified_at' => 'datetime',
		'active' => 'boolean',
		'last_login_time' => 'datetime',
	];

	public function member() {
		return $this->belongsTo(Member::class);
	}

	public function savings() {
		return $this->hasMany(Saving::class);
	}

	public function loans() {
		return $this->hasMany(Loan::class);
	}

	public function purchases() {
		return $this->hasMany(Purchase::class);
	}

	public function sales() {
		return $this->hasMany(Sale::class);
	}

	public function isSuperAdmin(): bool {
		return $this->hasRole('super-admin');
	}
}
