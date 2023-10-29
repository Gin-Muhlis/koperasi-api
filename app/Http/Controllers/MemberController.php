<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Member;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\MemberResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreMemberRequest;
use App\Repositories\User\UserRepository;
use App\Http\Requests\UpdateMemberRequest;
use App\Repositories\Member\MemberRepository;

class MemberController extends Controller
{
    private $memberRepo;
    private $userRepo;

    public function __construct(MemberRepository $member, UserRepository $user)
    {
        $this->memberRepo = $member;
        $this->userRepo = $user;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data_members = $this->memberRepo->getmembers();

            return response()->json([
                'success' => true,
                'data' => MemberResource::collection($data_members)
            ]);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemberRequest $request)
    {
        try {
            $validated = $request->validated();

            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('public/member');
            }

            $data_member = $this->generateDataMember('store', $validated);

            DB::beginTransaction();

            $member = $this->memberRepo->createMember($data_member);

            $data_user = $this->generateDataUser('store', $member, $validated);

            $this->userRepo->createUser($data_user);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data member berhasil ditambahkan'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $member = $this->memberRepo->showMember($id);
            return response()->json([
                'success' => true,
                'data' => new MemberResource($member)
            ]);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
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

            $data_member = $this->generateDataMember('update', $validated);

            DB::beginTransaction();

            $this->memberRepo->updateMember($id, $data_member);

            $data_user = $this->generateDataUser('update', $member, $validated);

            $this->userRepo->updateUser($member->id, $data_user);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data member berhasil diedit'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $this->memberRepo->deleteMember($id);

            return response()->json([
                'success' => true,
                'message' => 'Data member berhasil dihapus'
            ]);
        } catch (Exception $e) {

            return $this->errorResponse($e->getMessage());
        }
    }

    private function errorResponse($error)
    {
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan dengan sistem',
            'error' => $error,
        ], 500);
    }

    private function generateDataMember($mode, $validated)
    {
        if ($mode == 'store') {
            $min = 1000000000;
            $max = 9999999999;

            $random_number = mt_rand($min, $max);

            return [
                'uuid' => Str::uuid(),
                'name' => $validated['name'],
                'email' => $validated['email'],
                'address' => $validated['address'],
                'phone_number' => $validated['phone_number'],
                'gender' => $validated['gender'],
                'identity_number' => str_pad($random_number, 10, '0', STR_PAD_LEFT),
                'religion' => $validated['religion'],
                'image' => $validated['image'],
                'date_activation' => Carbon::now()->format('Y-m-d')
            ];
        } else if ($mode == 'update') {
            return [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'address' => $validated['address'],
                'phone_number' => $validated['phone_number'],
                'gender' => $validated['gender'],
                'religion' => $validated['religion'],
                'image' => $validated['image'],
            ];
        }

        return true;
    }

    private function generateDataUser($mode, $member, $validated)
    {
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
