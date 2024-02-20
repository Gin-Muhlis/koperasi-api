<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Installment extends Model {
	use HasFactory;

	protected $fillable = ['uuid', 'code', 'amount', 'date', 'loan_id', 'user_id', 'invoice_id', 'sub_category_id'];

	public function loan() {
		return $this->belongsTo(Loan::class);
	}

	public function subCategory() {
		return $this->belongsTo(SubCategory::class);
	}
}
