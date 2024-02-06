<?php

namespace App\Http\Controllers;

require_once app_path() . '/Helpers/helpers.php';

use App\Http\Resources\MandatoryResource;
use App\Http\Resources\PrincipalSavingResource;
use App\Http\Resources\ReceivableResource;
use App\Repositories\Member\MemberRepository;
use App\Repositories\Saving\SavingRepository;
use App\Repositories\SubCategory\SubCategoryRepository;
use Exception;

class TabController extends Controller {
	private $memberRepo;
	private $savingRepo;
	private $subCategoryRepo;

	public function __construct(MemberRepository $memberRepository, SavingRepository $savingRepository, SubCategoryRepository $subCategoryRepository) {
		$this->memberRepo = $memberRepository;
		$this->savingRepo = $savingRepository;
		$this->subCategoryRepo = $subCategoryRepository;
	}

	public function principalSaving() {
		try {
			$sub_category = $this->subCategoryRepo->getByName('simpanan pokok');
			$members = $this->memberRepo->getMembers();

			$member_principaL_saving = [];
			$filtered_members = $this->filterMember(($members));
			foreach ($filtered_members as $member) {
				$member_savings = $this->savingRepo->getMemberSpesificSavings($member->id, $sub_category->id);

				if (count($member_savings) < 1) {
					$member_principaL_saving[] = $member;
				}
			}

			return response()->json([
				'data' => PrincipalSavingResource::collection($member_principaL_saving),
			]);
		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}

	public function mandatorySaving() {
		try {
			$members = $this->memberRepo->getMembers();

			$filtered_members = $this->filterMember(($members));

			return response()->json([
				'data' => MandatoryResource::collection($filtered_members),
			]);
		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}

	public function receivable() {
		try {
			$members = $this->memberRepo->getMembers();

			$filtered_members = $this->filterMember(($members));

			return response()->json([
				'data' => ReceivableResource::collection($filtered_members),
			]);
		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}

	private function filterMember($data) {
		$filtered_members = [];

		foreach ($data as $member) {
			if (!$member->user->hasRole('super-admin')) {
				$filtered_members[] = $member;
			}
		}

		return $filtered_members;
	}
}
