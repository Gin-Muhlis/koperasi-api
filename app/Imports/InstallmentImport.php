<?php

namespace App\Imports;

use App\Models\Installment;
use App\Models\SubCategory;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class InstallmentImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $data) {
            Installment::create([
                'uuid' => Str::uuid(),
                'code' => $this->generateCode(),
                'loan_id' => $data['loan_id'],
                'sub_category_id' => $this->getSubCategory($data['sub_category']),
                'amount' => $data['amount'],
                'date' => Carbon::createFromFormat('d-m-Y', $data['date'])->format('Y-m-d'),
            ]);
        }

    }

    private function generateCode()
    {
        $min = 1000000000;
        $max = 9999999999;

        $random_number = mt_rand($min, $max);

        return str_pad($random_number, 10, '0', STR_PAD_LEFT);
    }

    private function getSubCategory($name) {
        $subCategory = SubCategory::whereName($name)->first();

        return $subCategory->id;
    }   
}
