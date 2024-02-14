<?php

namespace App\Imports;

use App\Models\PositionCategory;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PositionCategoryImport implements ToCollection, WithHeadingRow {
	/**
	 * @param Collection $collection
	 */
	public function collection(Collection $collection) {
		foreach ($collection as $data) {
			PositionCategory::create([
				'position' => $data['position'],
				'pokok' => $data['pokok'],
				'wajib' => $data['wajib'],
				'wajib_khusus' => $data['wajib_khusus'],
			]);
		}
	}
}
