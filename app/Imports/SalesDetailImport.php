<?php

namespace App\Imports;

use App\Models\SalesDetail;
use App\Models\Stuff;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class SalesDetailImport implements ToCollection, WithHeadingRow
{
    /**
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
                'stuff_id' => $this->getStuff($data['stuff']),
                
            ]);
        }
    }

    private function getStuff($name) {
        $stuff = Stuff::whereName($name)->first();

        return $stuff->id;
    }
}
