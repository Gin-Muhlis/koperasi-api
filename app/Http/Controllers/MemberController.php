<?php

namespace App\Http\Controllers;

require_once app_path() . '/Helpers/helpers.php';

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\MemberResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreMemberRequest;
use App\Repositories\Role\RoleRepository;
use App\Repositories\User\UserRepository;
use App\Http\Requests\UpdateMemberRequest;
use App\Repositories\Member\MemberRepository;
use App\Repositories\Saving\SavingRepository;
use App\Repositories\SubCategory\SubCategoryRepository;

class MemberController extends Controller
{
    private $memberRepo;
    private $userRepo;
    private $savingRepo;
    private $roleRepo;
    private $subCategoryRepo;

    public function __construct(MemberRepository $member, UserRepository $user, SavingRepository $savingRepository, RoleRepository $roleRepository, SubCategoryRepository $subCategoryRepository)
    {
        $this->memberRepo = $member;
        $this->userRepo = $user;
        $this->savingRepo = $savingRepository;
        $this->roleRepo = $roleRepository;
        $this->subCategoryRepo = $subCategoryRepository;
    }

    public function index(Request $request)
    {
        try {
            $members = $this->memberRepo->getmembers();
            return response()->json([
                'data' => MemberResource::collection($members),
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    public function store(StoreMemberRequest $request)
    {
        try {
            $validated = $request->validated();

            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('public/member');
            }

            $data_member = generateDataMember('store', null, $validated);

            DB::beginTransaction();

            $member = $this->memberRepo->createMember($data_member);

            $data_user = generateDataUser('store', $member, $validated);

            $user = $this->userRepo->createUser($data_user);

            $role = $this->roleRepo->searchRole($validated['role']);

            if (is_null($role)) {
                return response()->json([
                    'message' => "Role {$validated['role']} tidak ditemukan"
                ], 400);
            }

            $user->assignRole($role->name);

            DB::commit();

            return response()->json([
                'role' => $validated['role'],
                'message' => 'Data angggota berhasil ditambahkan',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return errorResponse($e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $member = $this->memberRepo->showMember($id);
            return response()->json([
                'data' => new MemberResource($member),
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    public function update(UpdateMemberRequest $request, $id)
    {
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

            $this->userRepo->updateUser($member->user->id, $data_user);

            $role = $this->roleRepo->searchRole($validated['role']);

            if (is_null($role)) {
                return response()->json([
                    'message' => "Role {$validated['role']} tidak ditemukan"
                ], 400);
            }

            if ($member->user->getRoleNames()->first() != $validated['role']) {
                $member->user->syncRoles([$validated['role']]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Data anggota berhasil diperbarui',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return errorResponse($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->memberRepo->deleteMember($id);

            return response()->json([
                'message' => 'Data member berhasil dihapus',
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    public function getSavingMember()
    {
        try {
            $sub_categories = $this->subCategoryRepo->getSubCategories();
            $member = $this->memberRepo->showMember(auth()->user()->id);

            $filtered_sub_categories = filterSavingCategories($sub_categories);

            $detail_savings = [];
            $history_savings = [];
            $total_saving = 0;

            foreach ($member->savings as $saving) {
                $total_saving += $saving->amount;
                $history_savings[] = [
                    'id' => $saving->id,
                    'amount' => $saving->amount,
                    'sub_category' => $saving->subCategory->name,
                    'date' => $saving->date->toDateString(),
                    'status' => $saving->status
                ];
            }

            foreach ($filtered_sub_categories as $sub_category) {

                // simpanan
                $total = 0;
                foreach ($member->savings as $saving) {
                    if ($saving->sub_category_id == $sub_category->id) {
                        $total += $saving->amount;
                    }
                }

                $detail_savings[$sub_category->name] = $total;
            }

            $sorted_history = collect($history_savings)->sortByDesc('date')->values()->all();

            $data = [
                'id' => $member->id,
                'name' => $member->name,
                'position' => $member->position,
                'total_saving' => $total_saving,
                'detail_savings' => $detail_savings,
                'history_savings' => $sorted_history
            ];

            return response()->json([
                'data' => $data
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    public function getLoanMember()
    {
        try {
            $member = $this->memberRepo->showMember(auth()->user()->id);


            $data_dinamis = [];

            // pinjaman
            $total = 0;
            $loan_progress = false;


            foreach ($member->loans as $loan) {
                $total += $loan->total_payment;
                if ($loan->status != 'lunas') {
                    $loan_progress = true;
                }

                $detail = [
                    'code' => $loan->code,
                    'sub_category' => $loan->subCategory->name,
                    'total_loan' => $loan->total_payment,
                    'paid' => handlePaid($loan->installments),
                    'remain_payment' => $loan->total_payment - handlePaid($loan->installments),
                    'duration' => $loan->loan_duration,
                    'deadline' => $loan->deadline->toDateString(),
                    'date_completion' => $loan->date_completion ? $loan->date_completion->toDateString() : null,
                    'status' => $loan->status,
                ];
                $data_dinamis[] = $detail;
            }

            $data = [
                'total_loan' => $total,
                'detail_loans' => $data_dinamis
            ];

            return response()->json([
                'data' => $data
            ]);
        } catch (Exception $e) {
            return errorResponse(($e->getMessage()));
        }
    }

}
