<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class SubCategoryImport implements ToCollection, WithHeadingRow
{
    /**
     * import dummy data untuk tabel sub_categories/sub kategori
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $data) {
            $min = 1000000000;
            $max = 9999999999;

            $random_number = mt_rand($min, $max);

            $category = Category::where('name', $data['category'])->first();

            SubCategory::create([
                'uuid' => Str::uuid(),
                'code' => str_pad($random_number, 10, '0', STR_PAD_LEFT),
                'type' => $data['type'],
                'name' => $data['name'],
                'category_id' => $category->id,
                'type_payment' => $data['type_payment'],
            ]);
        }
    }
}
