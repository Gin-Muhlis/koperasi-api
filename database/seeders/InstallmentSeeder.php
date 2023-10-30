<?php

namespace Database\Seeders;

use App\Imports\InstallmentImport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class InstallmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = base_path('database/data/demoInstallment.xlsx');
        
        Excel::import(new InstallmentImport, $file);
    }
}
