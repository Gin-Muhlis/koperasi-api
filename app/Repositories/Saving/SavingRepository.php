<?php

namespace App\Repositories\Saving;

use LaravelEasyRepository\Repository;

interface SavingRepository extends Repository {

	public function getMemberSpesificSavings($member_id, $sub_category_id);
	public function makeSavingMembers($data);

	public function getSavingByInvoiceId($invoice_id);

	public function updateStatusSaving($id);
}
