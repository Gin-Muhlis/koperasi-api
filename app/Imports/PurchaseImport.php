<?php

namespace App\Imports;

require_once app_path() . '/Helpers/helpers.php';

use App\Models\Purchase;
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
                'sub_category_id' => getSubCategory($data['sub_category']),
                'date_purchase' => Carbon::now()->format('Y-m-d'),
                'code' => generateCode(),
                'total' => $data['total'],
                'user_id' => '1'
            ]);
        }
    }
}
