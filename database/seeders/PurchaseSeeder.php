<?php

namespace Database\Seeders;

use App\Imports\PurchaseImport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Memanggil file dummy data
        $file = base_path('database/data/demoPurchase.xlsx');
        
        // memanggil import untuk menjalankan dummy data
        Excel::import(new PurchaseImport, $file);
    }
}
