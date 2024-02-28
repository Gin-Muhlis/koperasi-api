<?php

namespace App\Http\Controllers\Auth;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Storage;

require_once app_path() . '/Helpers/helpers.php';

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\ProfileResource;
use App\Models\User;
use App\Repositories\Member\MemberRepository;
use App\Repositories\User\UserRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller {
	private $memberRepo;
	private $userRepo;

	public function __construct(MemberRepository $member, UserRepository $user) {
		$this->memberRepo = $member;
		$this->userRepo = $user;
	}
	public function register(RegisterRequest $request) {
		try {

			$validated = $request->validated();
			$validated['username'] = explode(' ', $validated['name'])[0];

			if ($request->hasFile('image')) {
				$validated['image'] = $request->file('image')->store('public/member');
			}

			$data_member = generateDataMember('store', null, $validated);

			DB::beginTransaction();

			$member = $this->memberRepo->createMember($data_member);

			$data_user = generateDataUser('store', $member, $validated);

			$user = $this->userRepo->createUser($data_user);

			$user->assignRole('member');

			DB::commit();

			return response()->json([
				'message' => 'Pendaftaran Berhasil',
			]);
		} catch (Exception $e) {
			DB::rollBack();
			return errorResponse($e->getMessage());
		}
	}

	public function login(LoginRequest $request) {
		try {
			$credentials = $request->validated();

			$isUser = User::with('member')->where('email', $credentials['email'])->first();

			if (!$isUser || !Hash::check($credentials['password'], $isUser->password)) {
				return response()->json([
					'message' => 'Email atau Password salah',
				], 400);
			}

			Auth::guard('api')->setUser($isUser);

			$user = Auth::guard('api')->user();

			$token = $user->createToken('auth-token')->accessToken;

			return response()->json([
				'name' => $user->username,
				'role' => $user->getRoleNames()->first(),
				'imageProfile' => $user->member?->image ? str_replace('public/', '', url("storage/{$user->member->image}")) : config('app.url') . '/images/profile-default.png',
				'accessToken' => $token,
			]);
		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}

	public function logout(Request $request) {
		try {
			$request->user()->token()->revoke();

			return response()->json([
				'message' => 'Logout Berhasil',
			]);
		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}

	public function profile(Request $request) {
		try {
			$data_user = $request->user();
			return response()->json([
				'data' => new ProfileResource($data_user),
			]);
		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}	

	public function updateProfile(UpdateProfileRequest $request, $id) {
		try {
			$validated = $request->validated();
			$validated['password'] = null;

			$member = $this->memberRepo->showMember($id);
			$validated['active'] = $member->active;
			
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

			$this->userRepo->updateUser(Auth::user()->id, $data_user);

			DB::commit();

			return response()->json([
				'message' => 'Profile berhasil diperbarui',
			]);
		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}
}
