<?php

namespace App\Imports;

require_once app_path() . '/Helpers/helpers.php';

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
     *  import untuk dummy data tabel member/anggota
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
                'identity_number' => generateCode(),
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
}
