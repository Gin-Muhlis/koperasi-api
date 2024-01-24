<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 */
	public function run(): void {
		// membuat akun member untuk admin
		$member = Member::create([
			'uuid' => Str::uuid(),
			'name' => 'Fubuki Shirakami',
			'email' => 'fubuki@gmail.com',
			'address' => 'Tokyo, Jepang',
			'phone_number' => '0895325722289',
			'gender' => 'P',
			'identity_number' => '123456789',
			'religion' => 'islam',
			'image' => null,
			'date_activation' => Carbon::now()->format('Y-m-d'),
		]);

		// membuat user untuk admin
		User::create([
			'uuid' => Str::uuid(),
			'username' => 'Fubuking',
			'email' => 'fubuki@gmail.com',
			'password' => Hash::make('fubuki123'),
			'member_id' => $member->id,
		]);

	}
}
