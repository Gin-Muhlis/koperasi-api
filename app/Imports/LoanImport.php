<?php

namespace App\Imports;

require_once app_path() . '/Helpers/helpers.php';

use App\Models\Loan;
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
                'code' => generateCode(),
                'member_id' => getMember($data['member']),
                'amount' => $data['amount'],
                'sub_category_id' => getSubCategory($data['sub_category']),
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
    
}
