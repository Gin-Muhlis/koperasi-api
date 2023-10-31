<?php

namespace App\Imports;

use App\Models\Sale;
use App\Models\SubCategory;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class SaleImport implements ToCollection, WithHeadingRow
{
    /**
     * import dummy data untuk tabel sales/penjualan
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $data) {
          
            Sale::create([
                'uuid' => Str::uuid(),
                'sub_category_id' => $this->getSubCategory($data['sub_category']),
                'code' => $this->generateCode(),
                'date' => Carbon::now()->format('Y-m-d'),
                'total_payment' => $data['total_payment'],
                'user_id' => 1
            ]);
        }
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
}
