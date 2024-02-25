<?php

namespace App\Http\Controllers;
use App\Repositories\Installment\InstallmentRepository;
use App\Repositories\Saving\SavingRepository;
use Illuminate\Support\Facades\Auth;

require_once app_path() . '/Helpers/helpers.php';

use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Http\Resources\MemberResource;
use App\Http\Resources\SavingMemberResource;
use App\Repositories\Member\MemberRepository;
use App\Repositories\User\UserRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MemberController extends Controller {
	private $memberRepo;
	private $userRepo;
	private $savingRepo;
	private $installmentRepo;

	public function __construct(MemberRepository $member, UserRepository $user, SavingRepository $savingRepository, InstallmentRepository $installmentRepository) {
		$this->memberRepo = $member;
		$this->userRepo = $user;
		$this->savingRepo = $savingRepository;
		$this->installmentRepo = $installmentRepository;
	}
	/**
	 * Display a listing of the resource.
	 */
	public function index(Request $request) {
		try {
			$members = $this->memberRepo->getmembers();

			$filtered_members = [];

			foreach ($members as $member) {
				if ($member->user->hasRole('member')) {
					$filtered_members[] = $member;
				}
			}

			return response()->json([
				'data' => MemberResource::collection($filtered_members),
			]);
		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(StoreMemberRequest $request) {
		try {
			$validated = $request->validated();

			if ($request->hasFile('image')) {
				$validated['image'] = $request->file('image')->store('public/member');
			}

			$data_member = generateDataMember('store', null, $validated);

			DB::beginTransaction();

			$member = $this->memberRepo->createMember($data_member);

			$data_user =generateDataUser('store', $member, $validated);

			$user = $this->userRepo->createUser($data_user);

			$user->assignRole($validated['role']);

			DB::commit();

			return response()->json([
				'message' => 'Data member berhasil ditambahkan',
			]);
		} catch (Exception $e) {
			DB::rollBack();
			return errorResponse($e->getMessage());
		}
	}

	/**
	 * Display the specified resource.
	 */
	public function show($id) {
		try {
			$member = $this->memberRepo->showMember($id);
			return response()->json([
				'data' => new MemberResource($member),
			]);
		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(UpdateMemberRequest $request, $id) {
		try {
			$validated = $request->validated();

			$member = $this->memberRepo->showMember($id);

			if ($request->hasFile('image')) {
				if ($member->image) {
					Storage::delete($member->image);
				}

				$validated['image'] = $request->file('image')->store('public/member');
			}

			$data_member = generateDataMember('update', $member, $validated);

			DB::beginTransaction();

			$this->memberRepo->updateMember($id, $data_member);

			$data_user = generateDataUser('update', $member, $validated);

			$this->userRepo->updateUser($member->id, $data_user);

			DB::commit();

			return response()->json([
				'message' => 'Data member berhasil diperbarui',
			]);
		} catch (Exception $e) {
			DB::rollBack();
			return errorResponse($e->getMessage());
		}
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy($id) {
		try {
			$this->memberRepo->deleteMember($id);

			return response()->json([
				'message' => 'Data member berhasil dihapus',
			]);
		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}

	public function reportSavingMembers() {
		try {
			$savingMembers = $this->memberRepo->getSavingMembers();

			$data = SavingMemberResource::collection($savingMembers);

			return response()->json([
				'success' => true,
				'data' => $data,
			]);
		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}

	public function dashboardMember() {
		try {
			$user = Auth::user();

			$total_mandatory_saving = 0;
			$total_special_mandatory_saving = 0;
			$total_voluntary_saving = 0;
			$total_recretional_saving = 0;

			$saving_members = $this->savingRepo->getSavingsMember($user->id);

			foreach ($saving_members as $saving) {
				if ($saving->subCategory->name = 'simpanan wajib') {
					$total_mandatory_saving += $saving->amount;
				} else if ($saving->subCategoryy->name = 'simpanan wajib khusus') {
					$total_special_mandatory_saving += $saving->amount;
				} else if ($saving->subCategoryy->name = 'simpanan sukarela') {
					$total_voluntary_saving += $saving->amount;
				} else if ($saving->subCategoryy->name = 'tabungan rekreasi') {
					$total_recretional_saving += $saving->amount;
				} 
			}

			$history_savings = $this->savingRepo->getHistorySavingmember($user->id);
			$history_isntallments = $this->installmentRepo->getHistoryInstallments($user->id);
			
			$data = [
				'total_mandatory_saving'=> $total_mandatory_saving,
				'total_special_mandatory_saving'=> $total_special_mandatory_saving,
				'total_voluntary_saving'=> $total_voluntary_saving,
				'total_recretional_saving'=> $total_recretional_saving,
				'history_savings' => $history_savings,
				'history_installments' => $history_isntallments
			];

			return response()->json([
				'data' => $data
			]);
		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}

	
}
