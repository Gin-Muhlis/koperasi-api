<?php

namespace App\Repositories\Member;

use App\Models\Member;
use Illuminate\Support\Facades\DB;
use LaravelEasyRepository\Implementations\Eloquent;

class MemberRepositoryImplement extends Eloquent implements MemberRepository
{

	protected $model;

	public function __construct(Member $model)
	{
		$this->model = $model;
	}

	public function getMembers()
	{
		return $this->model->with(['user', 'positionCategory'])->latest()->get();
	}

	public function createMember($request)
	{
		return $this->create($request);
	}

	public function showMember($id)
	{
		return $this->findOrFail($id);
	}

	public function updateMember($id, $request)
	{
		return $this->update($id, $request);
	}

	public function deleteMember($id)
	{
		return $this->delete($id);
	}

	public function getReportMembers()
	{
		return $this->model->with([
			'savings' => function ($query) {
				$query->with('subCategory');
			},
			'loans' => function ($query) {
				$query->with('subCategory', 'installments');
			}
		])->get();
	}

	public function getReportMember($id)
	{
		return DB::table('savings')
			->select(DB::raw('MONTH(date) as month'), 'member_id', 'sub_category_id', DB::raw('SUM(amount) as total_amount'))
			->where('member_id', $id)
			->groupBy('month', 'member_id', 'sub_category_id')
			->get();
	}

	public function getNotPaidMembers()
	{
		return $this->model->with([
			'loans' => function ($query) {
				$query->where([
					['status', '!=', 'lunas']
				]);
			}
		])->whereHas('loans', function ($query) {
			$query->where([
				['status', '!=', 'lunas']
			]);
		})->get();
	}

	public function getNotPaidMember($member_id)
	{
		return $this->model->whereHas('loans', function ($query) {
			$query->where([
				['status', '!=', 'lunas'],
			]);
		})->where('id', $member_id)->get();
	}

	public function getReportLoanMembers()
	{
		return $this->model->with([
			'loans' => function ($query) {
				$query->where([
					['status', '!=', 'lunas']
				]);
			}
		])->whereHas('loans', function ($query) {
			$query->where([
				['status', '!=', 'lunas']
			]);
		})->get();
	}

	public function getCountMembers()
	{
		return $this->model->count();
	}

}
