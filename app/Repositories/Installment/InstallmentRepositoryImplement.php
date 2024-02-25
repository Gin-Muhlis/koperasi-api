<?php

namespace App\Repositories\Installment;

use App\Models\Installment;
use LaravelEasyRepository\Implementations\Eloquent;

class InstallmentRepositoryImplement extends Eloquent implements InstallmentRepository {

	
	protected $model;

	public function __construct(Installment $model) {
		$this->model = $model;
	}

	public function makeInstallmentMembers($data) {
		return $this->create($data);
	}

	public function getMemberPaymentMonth($month, $loan_id) {
		return $this->model->whereMonth('date', $month)->where('loan_id', $loan_id)->get();
	}

	public function getSumPayment($loan_id) {
		return $this->model->where('loan_id', $loan_id)->sum('amount');
	}

	public function getInstalmentByInvoiceId($invoice_id) {
		return $this->model->where('invoice_id', $invoice_id)->get();
	}

	public function updateStatusIsntallment($id) {
		$this->model->where('id', $id)->update(['status' => 'dibayar']);
	}

	public function getHistoryInstallments($id) {
		return $this->model->whereHas('loan.member', function ($query) use ($id) {
			$query->where('members.id', $id);
		})->limit(4)->select('date', 'amount', 'code')->latest()->get();
	}
}
