<?php

namespace App\Repositories\Loan;

use App\Models\Loan;
use LaravelEasyRepository\Implementations\Eloquent;

class LoanRepositoryImplement extends Eloquent implements LoanRepository {

	protected $model;

	public function __construct(Loan $model) {
		$this->model = $model;
	}

	public function findLoan($id) {
		return $this->findOrFail($id);
	}

	public function updateStatusLoan($id, $data) {
		$this->update($id, $data);
	}

	public function getTotalLoans() {
		return $this->model->sum('total_payment');
	}

	public function createLoanMember($data) {
		$this->create($data);
	}
	public function getTotalLoanBySubcategory($sub_category_id, $member_id) {
		return $this->model->where([
			['sub_category_id', $sub_category_id],
			['member_id', $member_id]
		])->sum('amount');
	}
}
