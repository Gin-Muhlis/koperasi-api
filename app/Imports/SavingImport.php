<?php

namespace App\Imports;

require_once app_path() . '/Helpers/helpers.php';

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
            // dd($this->generateMonthYear($data['bulan_pembayaran']));
            Saving::create([
                'uuid' => Str::uuid(),
                'code' => generateCode(),
                'member_id' => getMember($data['nama_anggota']),
                'amount' => $data['barang'] ?? '0',
                'date' => Carbon::now()->format('Y-m-d'),
                'sub_category_id' => getSubCategory('piutang barang'),
                'month_year' => '11-2023',
                'user_id' => '1',
                'description' => '-'
            ]);
        }
    }


    
}
