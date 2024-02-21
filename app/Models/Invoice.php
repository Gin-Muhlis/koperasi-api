<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model {
	use HasFactory;

	protected $fillable = ['invoice_code', 'invoice_name', 'date', 'due_date', 'payment_source', 'status', 'payment_date', 'payment_method', 'user_id'];

	public function savings() {
		return $this->hasMany(Saving::class);
	}

	public function installments() {
		return $this->hasMany(Installment::class);
	}

	public function user() {
		return $this->belongsTo(User::class);
	}

	public function payment() {
		return $this->hasOne(Payment::class);
	}

}
