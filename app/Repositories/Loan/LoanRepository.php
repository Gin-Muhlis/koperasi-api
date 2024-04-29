<?php

namespace App\Repositories\Loan;

use LaravelEasyRepository\Repository;

interface LoanRepository extends Repository {

	public function findLoan($id);
	public function updateStatusLoan($id, $data);
	public function getTotalLoans();
	public function createLoanMember($data);
	public function getTotalLoanBySubcategory($sub_category_id, $member_id);
	public function getLoanMembers();
	public function getSumPaidLoanByMonth($month, $year);
	public function getSumNotPaidLoanByMonth($month, $year);
}
