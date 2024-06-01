<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Installment extends Model {
	use HasFactory, SoftDeletes;

	protected $fillable = ['uuid', 'amount', 'date', 'loan_id', 'member_id', 'user_id', 'invoice_id', 'sub_category_id', 'status'];

	public function loan() {
		return $this->belongsTo(Loan::class);
	}

	public function subCategory() {
		return $this->belongsTo(SubCategory::class);
	}
}
