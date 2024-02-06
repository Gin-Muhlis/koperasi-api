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
}
