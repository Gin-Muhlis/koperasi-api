<?php

namespace App\Imports;

use App\Models\Purchase;
use App\Models\SubCategory;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;
class PurchaseImport implements ToCollection, WithHeadingRow
{
    /**
     * import dummy data untuk tabel purchases/pembelian
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $data) {
            Purchase::create([
                'uuid' => Str::uuid(),
                'sub_category_id' => $this->getSubCategory($data['sub_category']),
                'date_purchase' => Carbon::now()->format('Y-m-d'),
                'code' => $this->generateCode(),
                'total' => $data['total'],
                'user_id' => '1'
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
