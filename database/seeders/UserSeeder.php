<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Member;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $member = Member::create([
            'uuid' => Str::uuid(),
            'name' => 'Fubuki Shirakami',
            'email' => 'fubuki17@gmail.com',
            'address' => 'Tokyo, Jepang',
            'phone_number' => '0895325722289',
            'gender' => 'P',
            'identity_number' => '123456789',
            'religion' => 'islam',
            'image' => 'public/member/profile.jpg',
            'date_activation' => Carbon::now()->format('Y-m-d')
        ]);

        User::create([
            'uuid' => Str::uuid(),
            'username' => 'Fubuking',
            'email' => 'fubuki17@gmail.com',
            'password' => Hash::make('fubuki123'),
            'member_id' => $member->id
        ]);

    }
}
