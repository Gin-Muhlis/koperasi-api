<?php

namespace App\Repositories\Loan;

use App\Models\Loan;
use LaravelEasyRepository\Implementations\Eloquent;

class LoanRepositoryImplement extends Eloquent implements LoanRepository {

	/**
	 * Model class to be used in this repository for the common methods inside Eloquent
	 * Don't remove or change $this->model variable name
	 * @property Model|mixed $model;
	 */
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
}
