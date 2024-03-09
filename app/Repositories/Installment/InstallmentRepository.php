<?php

namespace App\Repositories\Installment;

use LaravelEasyRepository\Repository;

interface InstallmentRepository extends Repository {

	public function makeInstallmentMembers($data);
	public function getMemberPaymentMonth($year, $month, $loan_id);
	public function getSumPayment($loan_id);

	public function getInstalmentByInvoiceId($invoice_id);

	public function updateStatusIsntallment($id);

	public function getHistoryInstallments($id);
	

}
