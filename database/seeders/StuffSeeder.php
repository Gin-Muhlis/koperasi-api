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
        // Memanggil file dummy data
        $file = base_path('database/data/demoStuff.xlsx');
        
        // memanggil import untuk menjalankan dummy data
        Excel::import(new StuffImport, $file);
    }
}
