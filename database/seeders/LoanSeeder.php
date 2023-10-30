<?php

namespace Database\Seeders;

use App\Imports\LoanImport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class LoanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = base_path('database/data/demoLoan.xlsx');
        
        Excel::import(new LoanImport, $file);
    }
}
