<?php

namespace Database\Seeders;

use App\Imports\ProfileAppImport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class ProfileAppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       	// Memanggil file dummy data
		$file = base_path('database/data/demoProfileApp.xlsx');

		// memanggil import untuk menjalankan dummy data
		Excel::import(new ProfileAppImport, $file);
    }
}
