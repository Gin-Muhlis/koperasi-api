<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model {
	use HasFactory;

	protected $fillable = ['uuid', 'member_id', 'principal_saving', 'mandatory_saving', 'special_mandatory_saving', 'voluntary_saving', 'recretional_saving', 'receivable', 'account_receivable', 'month_year', 'user_id'];

	public function member() {
		return $this->belongsTo(Member::class);
	}

	public function user() {
		return $this->belongsTo(User::class);
	}
}
