<?php

namespace App\Imports;

use App\Models\Member;
use App\Models\Saving;
use App\Models\SubCategory;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class SavingImport implements ToCollection, WithHeadingRow
{
    /**
     * import dummmy data untuk tabel savings/simpanan
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        
        foreach ($collection as $data) {
            Saving::create([
                'uuid' => Str::uuid(),
                'code' => $this->generateCode(),
                'member_id' => $this->getMember($data['nama_anggota']),
                'amount' => $data['barang'] ?? '0',
                'date' => Carbon::now()->format('Y-m-d'),
                'sub_category_id' => $this->getSubCategory('piutang barang'),
                'user_id' => '1',
                'description' => '-'
            ]);
        }
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

     /**
     * mencari member berdasarkan nama dan mereturn idnya
     *
     * @param  mixed $name
     * @return void
     */
    private function getMember($name) {
        $member = Member::whereName($name)->first();

        return $member->id;
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
}
