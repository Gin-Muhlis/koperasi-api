<?php

namespace Database\Seeders;

use App\Imports\SavingImport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class SavingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = base_path('database/data/demoSaving.xlsx');
        
        Excel::import(new SavingImport, $file);
    }
}
