<?php

namespace Database\Seeders;

use App\Imports\PositionCategoryImport;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class PositionCategorySeeder extends Seeder {
	/**
	 * Run the database seeds.
	 */
	public function run(): void {
		// Memanggil file dummy data
		$file = base_path('database/data/demoPositionCategory.xlsx');

		// memanggil import untuk menjalankan dummy data
		Excel::import(new PositionCategoryImport, $file);
	}
}
