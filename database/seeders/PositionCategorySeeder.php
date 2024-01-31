<?php

namespace Database\Seeders;

use App\Models\PositionCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PositionCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'position' => 'pns',
                'pokok' => 100000,
                'min_wajib' => 30000,
                'min_wajib_khusus' => 10000,
            ],
            [
                'position' => 'p3k',
                'pokok' => 50000,
                'min_wajib' => 20000,
                'min_wajib_khusus' => 5000,
            ], [
                'position' => 'cpns',
                'pokok' => 70000,
                'min_wajib' => 10000,
                'min_wajib_khusus' => 5000,
            ]
        ];

        foreach ($data as $item) {
            PositionCategory::create($item);
        }
    }
}
