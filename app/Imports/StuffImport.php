<?php

namespace App\Imports;

require_once app_path() . '/Helpers/helpers.php';

use App\Models\Product;
use App\Models\Stuff;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;
class StuffImport implements ToCollection, WithHeadingRow
{
    /**
     * import dummy data untuk tabel stuffs/barang
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $data) {
            Stuff::create([
                'uuid' => Str::uuid(),
                'name' => $data['name'],
                'price' => $data['price'],
                'product_id' => getProduct($data['product']),
            ]);
        }
    }
 
}
