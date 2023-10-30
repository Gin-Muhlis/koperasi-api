<?php

namespace Database\Seeders;

use App\Imports\CategoryImport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = base_path('database/data/demoCategory.xlsx');
        
        Excel::import(new CategoryImport, $file);
    }
}
