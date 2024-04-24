<?php

namespace App\Repositories\Payment;

use App\Models\Payment;
use LaravelEasyRepository\Implementations\Eloquent;

class PaymentRepositoryImplement extends Eloquent implements PaymentRepository {

	
	protected $model;

	public function __construct(Payment $model) {
		$this->model = $model;
	}

	public function createPayment($data) {
		$this->create($data);
	}
}
