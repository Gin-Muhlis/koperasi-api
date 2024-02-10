<?php

namespace App\Repositories\Installment;

use App\Models\Installment;
use LaravelEasyRepository\Implementations\Eloquent;

class InstallmentRepositoryImplement extends Eloquent implements InstallmentRepository {

	/**
	 * Model class to be used in this repository for the common methods inside Eloquent
	 * Don't remove or change $this->model variable name
	 * @property Model|mixed $model;
	 */
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
}
