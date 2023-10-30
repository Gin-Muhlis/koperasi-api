<?php

namespace Database\Seeders;

use App\Imports\StuffImport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class StuffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = base_path('database/data/demoStuff.xlsx');
        
        Excel::import(new StuffImport, $file);
    }
}
