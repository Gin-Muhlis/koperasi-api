<?php

namespace App\Http\Controllers\Auth;

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

			if ($request->hasFile('image')) {
				$validated['image'] = $request->file('image')->store('public/member');
			}

			$data_member = $this->generateDataMember($validated);

			DB::beginTransaction();

			$member = $this->memberRepo->createMember($data_member);

			$data_user = $this->generateDataUser($member, $validated);

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

			if (!$isUser) {
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

	private function generateDataMember($validated) {
		$min = 1000000000;
		$max = 9999999999;

		$random_number = mt_rand($min, $max);

		return [
			'uuid' => Str::uuid(),
			'name' => $validated['name'],
			'email' => $validated['email'],
			'address' => $validated['address'],
			'phone_number' => $validated['phone_number'],
			'position' => $validated['position'],
			'gender' => $validated['gender'],
			'identity_number' => str_pad($random_number, 10, '0', STR_PAD_LEFT),
			'religion' => $validated['religion'],
			'image' => $validated['image'],
			'date_activation' => Carbon::now()->format('Y-m-d'),
		];
	}

	private function generateDataUser($member, $validated) {
		$name_split = explode(' ', $member->name);
		$first_name = $name_split[0];
		$second_name = $name_split[1] ? $name_split[1] : '';
		$name = "{$first_name} {$second_name}";
		return [
			'username' => $name,
			'password' => Hash::make($validated['password']),
			'email' => $member->email,
			'uuid' => $member->uuid,
			'member_id' => $member->id,
			'active' => 1,
		];
	}
}
