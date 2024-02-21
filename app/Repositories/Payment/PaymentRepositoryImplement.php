<?php

namespace App\Repositories\Payment;

use App\Models\Payment;
use LaravelEasyRepository\Implementations\Eloquent;

class PaymentRepositoryImplement extends Eloquent implements PaymentRepository {

	/**
	 * Model class to be used in this repository for the common methods inside Eloquent
	 * Don't remove or change $this->model variable name
	 * @property Model|mixed $model;
	 */
	protected $model;

	public function __construct(Payment $model) {
		$this->model = $model;
	}

	public function createPayment($data) {
		$this->create($data);
	}
}
