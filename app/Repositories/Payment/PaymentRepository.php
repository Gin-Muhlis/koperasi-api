<?php

namespace App\Repositories\Payment;

use LaravelEasyRepository\Repository;

interface PaymentRepository extends Repository {

	public function createPayment($data);
}
