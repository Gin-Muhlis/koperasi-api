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
		return $this->model->with('subCategory')->where('member_id', $id)->limit(4)->latest()->get();
	}
	public function getSavingsMember($member_id) {
		return $this->model->with('subCategory')->where('member_id', $member_id)->get();
	}

	public function getTotalSavings() {
		return $this->model->sum('amount');
	}

	public function getTotalSavingBySubCategory($sub_category_id, $member_id) {
		return $this->model->where([
			['sub_category_id', $sub_category_id],
			['member_id', $member_id]
		])->sum('amount');
	}

	public function getNotPayedSaving($member_id, $sub_category_id, $time) {
		return $this->model->with('subCategory')->where([
			['sub_category_id', $sub_category_id],
			['member_id', $member_id],
			['month_year', $time]
		])->first();
	}
}
