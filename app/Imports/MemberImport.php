<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Member;
use App\Models\User;
use Carbon\Carbon;

class MemberImport implements ToCollection, WithHeadingRow  
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        
        foreach ($collection as $data) 
        {
        
            $member = Member::create([
                'uuid' => Str::uuid(),
                'name' => $data['name'],
                'email' => $data['email'],
                'address' => $data['address'],
                'phone_number' => $data['phone_number'],
                'gender' => $data['gender'],
                'identity_number' => $this->generateCode(),
                'religion' => $data['religion'],
                'date_activation' => Carbon::now()->format('Y-m-d')
            ]);

            User::create([
                'uuid' => Str::uuid(),
                'username' => $data['username'],
                'email' => $member->email,
                'password' => Hash::make($data['password']),
                'member_id' => $member->id
            ]);
        }
    }

    private function generateCode() {
        $min = 1000000000;
        $max = 9999999999;

        $random_number = mt_rand($min, $max);

        return str_pad($random_number, 10, '0', STR_PAD_LEFT);
    }
}
