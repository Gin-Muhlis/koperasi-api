<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model {
	use HasFactory;

	protected $fillable = ['uuid', 'invoice_id', 'amount', 'date_payment', 'image', 'no_rek', 'transfer_name'];

	public function invoice() {
		return $this->belongsTo(Invoice::class);
	}
}
