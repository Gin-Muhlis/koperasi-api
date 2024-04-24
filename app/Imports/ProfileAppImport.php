<?php

namespace App\Imports;

use App\Models\ProfileApp;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProfileAppImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $data) {
            ProfileApp::create([
                'uuid' => Str::uuid(),
                'chairmans_name' => $data['chairman_name'],
                'secretary_name' => $data['secretary_name'],
                'treasurer_name' => $data['treasurer_name'],
                'address' => $data['address'],
            ]);
        }
    }
}
