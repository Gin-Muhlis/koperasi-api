<?php

namespace Database\Seeders;

use App\Imports\SubCategoryImport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Memanggil file dummy data
        $file = base_path('database/data/demoSubCategory.xlsx');
        
        // memanggil import untuk menjalankan dummy data
        Excel::import(new SubCategoryImport, $file);
    }
}
