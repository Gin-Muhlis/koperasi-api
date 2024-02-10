<?php

namespace App\Http\Controllers;

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

	public function __construct(MemberRepository $member, UserRepository $user) {
		$this->memberRepo = $member;
		$this->userRepo = $user;
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

			$data_member = $this->generateDataMember('store', $validated);

			DB::beginTransaction();

			$member = $this->memberRepo->createMember($data_member);

			$data_user = $this->generateDataUser('store', $member, $validated);

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

			$data_member = $this->generateDataMember('update', $validated);

			DB::beginTransaction();

			$this->memberRepo->updateMember($id, $data_member);

			$data_user = $this->generateDataUser('update', $member, $validated);

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

	private function generateDataMember($mode, $validated) {
		if ($mode == 'store') {
			$min = 1000000000;
			$max = 9999999999;

			$random_number = mt_rand($min, $max);

			return [
				'uuid' => Str::uuid(),
				'name' => $validated['name'],
				'email' => $validated['email'],
				'address' => $validated['address'],
				'position' => $validated['position'],
				'phone_number' => $validated['phone_number'],
				'gender' => $validated['gender'],
				'identity_number' => str_pad($random_number, 10, '0', STR_PAD_LEFT),
				'religion' => $validated['religion'],
				'image' => $validated['image'],
				'date_activation' => Carbon::now()->format('Y-m-d'),
			];
		} else if ($mode == 'update') {
			return [
				'name' => $validated['name'],
				'email' => $validated['email'],
				'address' => $validated['address'],
				'position' => $validated['position'],
				'phone_number' => $validated['phone_number'],
				'gender' => $validated['gender'],
				'religion' => $validated['religion'],
				'image' => $validated['image'] ?? null,
			];
		}

		return true;
	}

	private function generateDataUser($mode, $member, $validated) {
		if ($mode == 'store') {
			return [
				'username' => $validated['username'],
				'password' => Hash::make($validated['password']),
				'email' => $member->email,
				'uuid' => $member->uuid,
				'member_id' => $member->id,
				'active' => 1,
			];
		} else if ($mode == 'update') {
			return [
				'username' => $validated['username'],
				'password' => $validated['password'] ? Hash::make($validated['password']) : $member->user->password,
				'email' => $validated['email'],
				'active' => $validated['active'],
			];
		}

		return true;
	}
}
