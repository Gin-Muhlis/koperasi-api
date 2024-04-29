<?php

namespace App\Repositories\Saving;

use LaravelEasyRepository\Repository;

interface SavingRepository extends Repository {

	public function getMemberSpesificSavings($member_id, $sub_category_id);
	public function makeSavingMembers($data);
	public function getSavingByInvoiceId($invoice_id);
	public function updateStatusSaving($id);
	public function getHistorySavingmember($id);
	public function getSavingsMember($member_id);
	public function getTotalSavings();
	public function getTotalSavingBySubCategory($sub_category_id, $member_id);
	public function getNotPayedSaving($member_id, $sub_category_id, $time);
}
