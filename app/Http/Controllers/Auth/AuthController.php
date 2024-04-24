<?php

namespace App\Http\Controllers\Auth;

require_once app_path() . '/Helpers/helpers.php';

use App\Http\Requests\ChangePasswordMemberRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\ProfileResource;
use App\Models\User;
use App\Repositories\Member\MemberRepository;
use App\Repositories\User\UserRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller {
	private $memberRepo;
	private $userRepo;

	public function __construct(MemberRepository $member, UserRepository $user) {
		$this->memberRepo = $member;
		$this->userRepo = $user;
	}

	// Register
	public function register(RegisterRequest $request) {
		try {
			// Mengambil validated
			$validated = $request->validated();

			// Mengambil nama pertama untuk username
			$validated['username'] = explode(' ', $validated['name'])[0];

			// handle image
			if ($request->hasFile('image')) {
				$validated['image'] = $request->file('image')->store('public/member');
			}

			// Membuat data member
			$data_member = generateDataMember('store', null, $validated);

			DB::beginTransaction();

			// Menyimpan data member
			$member = $this->memberRepo->createMember($data_member);

			// Membuat data user
			$data_user = generateDataUser('store', $member, $validated);

			// Menyimpan data user
			$user = $this->userRepo->createUser($data_user);

			// Assign role member untuk yang baru daftar
			$user->assignRole('member');

			DB::commit();

			return response()->json([
				'message' => 'Pendaftaran Berhasil',
			]);
		} catch (Exception $e) {
			// Tarik data kembali jika terjadi kesalahan
			DB::rollBack();
			return errorResponse($e->getMessage());
		}
	}

	// Login
	public function login(LoginRequest $request) {
		try {
			// Mengambil validated (email, password)
			$credentials = $request->validated();

			// Mencari user
			$isUser = User::with('member')->where('email', $credentials['email'])->first();

			// Mengecek email dan password user apakah sesuai
			if (!$isUser || !Hash::check($credentials['password'], $isUser->password)) {
				return response()->json([
					'message' => 'Email atau Password salah',
				], 400);
			}

			// Loginkan user
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

	// Logout
	public function logout(Request $request) {
		try {
			// Menghapus token
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
			// Mengambil data user yang login
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
			// Mengambil validated
			$validated = $request->validated();
			// Mengisi key password dengan null
			$validated['password'] = null;

			// mengambil member berdasarkan id
			$member = $this->memberRepo->showMember($id);
			$validated['active'] = $member->active;
			
			// jika user mengirimkan gambar
			if ($request->hasFile('image')) {
				// Menghapus gambar jika user memiliki gambar sebelumnya
				if ($member->image) {
					Storage::delete($member->image);
				}

				// Menyimpan gambar
				$validated['image'] = $request->file('image')->store('public/member');
			}

			// Membuat data member
			$data_member = generateDataMember('update', $member, $validated);

			DB::beginTransaction();

			// Mengupdate data member
			$this->memberRepo->updateMember($id, $data_member);

			// Membuat data user
			$data_user = generateDataUser('update', $member, $validated);

			// Mengupdate data user
			$this->userRepo->updateUser(Auth::user()->id, $data_user);

			DB::commit();

			return response()->json([
				'message' => 'Profile berhasil diperbarui',
			]);
		} catch (Exception $e) {
			DB::rollBack();
			return errorResponse($e->getMessage());
		}
	}

	// Ganti password oleh admin
	public function changePassword(ChangePasswordRequest $request, $id) {
		try {
			// Mengambil validated
			$validated = $request->validated();

			// Mengganti password dengan hash
			$validated['password'] = Hash::make($validated['password']);

			// Mengambil user
			$user = $this->userRepo->getUserByMemberId($id);

			// Membuat data user
			$data = [
				...$user->toArray(),
				'password' => $validated['password'],
			];

			// Merubah password user
			$this->userRepo->changePassword($data, $id);
			
			return response()->json([
				'message' => 'Password berhasil diperbarui'
			]);
		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}

	// Ganti password oleh member
	public function changePasswordMember(ChangePasswordMemberRequest $request) {
		try {
			// Mengambil user yang login
			$user = Auth::user();

			// mengambil validated
			$validated = $request->validated();

			// Mengecek apakah password yang sekarang diinputkan sama dengan yang tersimpan di database
			if (!Hash::check($validated['current_password'], $user->password)) {
				return redirect()->back()->with('error', 'Password saat ini salah.');
			}

			// Meng hash password
			$validated['password'] = Hash::make($validated['password']);

			// Membuat data user
			$data = [
				...$user->toArray(),
				'password' => $validated['password'],
			];

			// Merubah password user
			$this->userRepo->changePassword($data, $user->id);
			
			return response()->json([
				'message' => 'Password berhasil diperbarui'
			]);
		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}
}
