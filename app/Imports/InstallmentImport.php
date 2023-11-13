<?php

namespace App\Imports;

require_once app_path() . '/Helpers/helpers.php';

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
     * import untuk dummy data tabel installments/angsuran
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $data) {
            Installment::create([
                'uuid' => Str::uuid(),
                'code' => generateCode(),
                'loan_id' => $data['loan_id'],
                'sub_category_id' => $this->getSubCategory($data['sub_category']),
                'amount' => $data['amount'],
                'date' => Carbon::createFromFormat('d-m-Y', $data['date'])->format('Y-m-d'),
            ]);
        }

    }

        
    /**
     * generate angka random untuk code
     *
     * @return string
     */
    
    
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
