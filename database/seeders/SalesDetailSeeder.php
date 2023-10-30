<?php

namespace Database\Seeders;

use App\Imports\SalesDetailImport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class SalesDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = base_path('database/data/demoSaleDetail.xlsx');
        
        Excel::import(new SalesDetailImport, $file);
    }
}
