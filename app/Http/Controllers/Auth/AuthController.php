<?php

namespace App\Http\Controllers\Auth;

require_once app_path() . '/Helpers/helpers.php';

use Exception;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use App\Repositories\User\UserRepository;
use App\Repositories\Member\MemberRepository;

class AuthController extends Controller
{
    private $memberRepo;
    private $userRepo;

    public function __construct(MemberRepository $member, UserRepository $user)
    {
        $this->memberRepo = $member;
        $this->userRepo = $user;
    }
    public function register(RegisterRequest $request)
    {
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
                'success' => true,
                'message' => 'Pendaftaran Berhasil'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return errorResponse($e->getMessage());
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->validated();

            $isUser = User::where('email', $credentials['email'])->first();

            if (!Auth::guard('api')->setUser($isUser)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email atau Password salah'
                ], 400);
            }

            $user = Auth::guard('api')->user();

            $token = $user->createToken('auth-token')->accessToken;

            return response()->json([
                'success' => true,
                'data' => $user,
                'token' => $token
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->token()->revoke();

            return response()->json([
                'message' => 'Logout Berhasil'
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    private function generateDataMember($validated)
    {
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
    }

    private function generateDataUser($member, $validated)
    {
        return [
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'email' => $member->email,
            'uuid' => $member->uuid,
            'member_id' => $member->id,
            'active' => 1,
        ];
    }
}
