<?php

namespace App\Repositories\Loan;

use LaravelEasyRepository\Repository;

interface LoanRepository extends Repository {

	public function findLoan($id);
	public function updateStatusLoan($id, $data);
}
