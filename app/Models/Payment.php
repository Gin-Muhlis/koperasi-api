<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model {
	use HasFactory;

	protected $fillable = ['uuid', 'invoice_id', 'amount', 'date_payment', 'no_rek', 'payer', 'payment_method'];

	public function invoice() {
		return $this->belongsTo(Invoice::class);
	}
}
