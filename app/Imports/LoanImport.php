<?php

namespace App\Imports;

use App\Models\Loan;
use App\Models\Member;
use App\Models\SubCategory;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class LoanImport implements ToCollection, WithHeadingRow
{
    /**
     * import untuk dummy data table loans/pinjaman
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        // dd($collection);
        foreach ($collection as $data) {
            Loan::create([
                'uuid' => Str::uuid(),
                'code' => $this->generateCode(),
                'member_id' => $this->getMember($data['member']),
                'amount' => $data['amount'],
                'sub_category_id' => $this->getSubCategory($data['sub_category']),
                'user_id' => '1',
                'date' => Carbon::now()->format('Y-m-d'),
                'loan_duration' => $data['loan_duration'],
                'loan_interest' => $data['loan_interest'],
                'total_payment' => $data['total_payment'],
                'status' => $data['status'],
                'deadline' => Carbon::createFromFormat('d-m-Y', $data['deadline'])->format('Y-m-d'),
                'date_completion' => $data['date_completion'],
                'description' => $data['description']
            ]);
        }
    }
    
    /**
     * generate angka random untuk code
     *
     * @return string
     */
    private function generateCode()
    {
        $min = 1000000000;
        $max = 9999999999;

        $random_number = mt_rand($min, $max);

        return str_pad($random_number, 10, '0', STR_PAD_LEFT);
    }
    
    /**
     * mencari member berdasarkan nama dan mereturn idnya
     *
     * @param  mixed $name
     * @return void
     */
    private function getMember($name) {
        $member = Member::whereName($name)->first();

        return $member->id;
    }

      /**
     * mencari sub kategori berdasakan nama dan mereturn idnya
     *
     * @param  mixed $name
     * @return number
     */
    private function getSubCategory($name) {
        $subCategory = SubCategory::whereName($name)->first();

        return $subCategory->id;
    }   
}
