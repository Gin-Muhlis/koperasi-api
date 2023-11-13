<?php

namespace App\Imports;

require_once app_path() . '/Helpers/helpers.php';

use App\Models\SalesDetail;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class SalesDetailImport implements ToCollection, WithHeadingRow
{
    /**
     * import dummy data untuk tabel salesDetails/penjualan detail
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $data) {
            SalesDetail::create([
                'uuid' => Str::uuid(),
                'sale_id' => $data['sale_id'],
                'qty' => $data['qty'],
                'unit_price' => $data['unit_price'],
                'sub_total' => $data['sub_total'],
                'stuff_id' => getStuff($data['stuff']),
            ]);
        }
    }
}
