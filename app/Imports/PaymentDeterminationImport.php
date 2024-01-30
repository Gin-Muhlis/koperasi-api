<?php

namespace App\Imports;

use App\Models\Member;
use App\Models\PaymentDetermination;
use App\Models\SubCategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PaymentDeterminationImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $data) {
            $member = Member::where('name', $data['member'])->first();
            $sub_category = SubCategory::where('name', $data['sub_category'])->first();
            PaymentDetermination::create([
                'uuid' => Str::uuid(),
                'member_id' => $member->id,
                'sub_category_id' => $sub_category->id,
                'amount' => $data['amount'],
                'payment_month' => $data['payment_month']
            ]);
        }
    }
}
