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
        $file = base_path('database/data/demoPurchase.xlsx');
        
        Excel::import(new PurchaseImport, $file);
    }
}
