<?php

namespace Database\Seeders;

use App\Imports\MemberImport;
use Illuminate\Database\Seeder;

use Maatwebsite\Excel\Facades\Excel;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Memanggil file dummy data
        $file = base_path('database/data/demoMember.xlsx');
        
        // memanggil import untuk menjalankan dummy data
        Excel::import(new MemberImport, $file);
    }
}
