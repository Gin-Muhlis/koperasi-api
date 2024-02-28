<?php

namespace App\Repositories\Member;

use App\Models\Member;
use LaravelEasyRepository\Implementations\Eloquent;

class MemberRepositoryImplement extends Eloquent implements MemberRepository {

	protected $model;

	public function __construct(Member $model) {
		$this->model = $model;
	}

	public function getMembers() {
		return $this->model->with(['user', 'positionCategory'])->latest()->get();
	}

	public function createMember($request) {
		return $this->create($request);
	}

	public function showMember($id) {
		return $this->findOrFail($id);
	}

	public function updateMember($id, $request) {
		return $this->update($id, $request);
	}

	public function deleteMember($id) {
		return $this->delete($id);
	}

	public function getReportMembers() {
		return $this->model->with(['savings' => function ($query) {
			$query->with('subCategory');
		}, 'loans' => function ($query) {
			$query->with('subCategory', 'installments');
		}])->get();
	}

	public function getReportMember($id) {
		return $this->model->with(['savings' => function ($query) {
			$query->with('subCategory');
		}, 'loans' => function ($query) {
			$query->with('subCategory', 'installments');
		}])->where('id', $id)->first();
	}

	public function getNotPaidMembers($sub_category) {
		return $this->model->with(['loans' => function ($query) use ($sub_category) {
			$query->where([
				['status', '!=', 'lunas'],
				['sub_category_id', $sub_category],
			]);
		}])->whereHas('loans', function ($query) use ($sub_category) {
			$query->where([
				['status', '!=', 'lunas'],
				['sub_category_id', $sub_category],
			]);
		})->get();
	}

	public function getNotPaidMember($member_id) {
		return $this->model->whereHas('loans', function ($query) {
			$query->where([
				['status', '!=', 'lunas'],
			]);
		})->where('id', $member_id)->get();
	}

	public function getReportLoanMembers() {
		return $this->model->with(['loans' => function ($query) {
			$query->where([
				['status', '!=', 'lunas']
			]);
		}])->whereHas('loans', function ($query) {
			$query->where([
				['status', '!=', 'lunas']
			]);
		})->get();
	}

	public function getCountMembers() {
		return $this->model->whereHas('user.roles', function ($query) {
			$query->where('name', 'member');
		})->count();
	}
}
