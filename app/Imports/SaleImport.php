<?php

namespace App\Imports;

require_once app_path() . '/Helpers/helpers.php';

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
                'sub_category_id' => getSubCategory($data['sub_category']),
                'code' => generateCode(),
                'date' => Carbon::now()->format('Y-m-d'),
                'total_payment' => $data['total_payment'],
                'user_id' => 1
            ]);
        }
    }
}
