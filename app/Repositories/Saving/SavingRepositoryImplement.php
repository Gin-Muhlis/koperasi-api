<?php

namespace App\Repositories\Saving;

use App\Models\Saving;
use LaravelEasyRepository\Implementations\Eloquent;

class SavingRepositoryImplement extends Eloquent implements SavingRepository {

	protected $model;

	public function __construct(Saving $model) {
		$this->model = $model;
	}

	public function makeSavingMembers($data) {
		return $this->create($data);
	}

	public function getMemberSpesificSavings($member_id, $sub_category_id) {
		return $this->model->with('subCategory')->where([
			['member_id', $member_id],
			['sub_category_id', $sub_category_id],
		])->get();
	}

	public function getSavingByInvoiceId($invoice_id) {
		return $this->model->where('invoice_id', $invoice_id)->get();
	}

	public function updateStatusSaving($id) {
		$this->model->where('id', $id)->update(['status' => 'dibayar']);
	}

	public function getHistorySavingmember($id) {
		return $this->model->where('member_id', $id)->limit(4)->select('date', 'amount', 'code')->latest()->get();
	}
	public function getSavingsMember($member_id) {
		return $this->model->with('subCategory')->where('member_id', $member_id)->get();
	}
}
