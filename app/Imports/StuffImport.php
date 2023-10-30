<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Stuff;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;
class StuffImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $data) {
            Stuff::create([
                'uuid' => Str::uuid(),
                'name' => $data['name'],
                'price' => $data['price'],
                'product_id' => $this->getProduct($data['product']),
            ]);
        }
    }

    private function getProduct($name) {
        $product = Product::whereName($name)->first();

        return $product->id;
    }
}
