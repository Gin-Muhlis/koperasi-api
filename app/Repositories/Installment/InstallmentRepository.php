<?php

namespace App\Repositories\Installment;

use LaravelEasyRepository\Repository;

interface InstallmentRepository extends Repository {

	public function makeInstallmentMembers($data);
	public function getMemberPaymentMonth($month, $loan_id);
	public function getSumPayment($loan_id);
}
