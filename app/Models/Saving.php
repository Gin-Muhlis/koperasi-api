<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Saving extends Model {
	use HasFactory, SoftDeletes;

	protected $fillable = [
		'uuid',
		'amount',
		'date',
		'member_id',
		'sub_category_id',
		'user_id',
		'invoice_id',
		'description',
		'month_year',
		'status',
	];

	protected $casts = [
		'date' => 'date',
	];

	public function member() {
		return $this->belongsTo(Member::class);
	}

	public function subCategory() {
		return $this->belongsTo(SubCategory::class);
	}

	public function user() {
		return $this->belongsTo(User::class);
	}
}
