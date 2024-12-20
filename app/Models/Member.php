<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
	use HasFactory, SoftDeletes;

	protected $fillable = [
		'uuid',
		'email',
		'name',
		'address',
		'position',
		'group_id',
		'phone_number',
		'gender',
		'religion',
		'image',
		'date_activation',
		'user_id',
	];


	public function user()
	{
		return $this->hasOne(User::class);
	}

	public function savings()
	{
		return $this->hasMany(Saving::class);
	}

	public function loans()
	{
		return $this->hasMany(Loan::class);
	}

	public function positionCategory()
	{
		return $this->belongsTo(PositionCategory::class, 'group_id');
	}
}
