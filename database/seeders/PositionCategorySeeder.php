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
                'position' => 'PNS Golongan IV',
                'pokok' => 25000,
                'wajib' => 60000,
                'wajib_khusus' => 10000,
            ],
            [
                'position' => 'PNS Golongan III',
                'pokok' => 20000,
                'wajib' => 40000,
                'wajib_khusus' => 7500,
            ], [
                'position' => 'p3k',
                'pokok' => 15000,
                'wajib' => 25000,
                'wajib_khusus' => 5000,
            ], [
                'position' => 'honor',
                'pokok' => 15000,
                'wajib' => 25000,
                'wajib_khusus' => 5000,
            ]
        ];

        foreach ($data as $item) {
            PositionCategory::create($item);
        }
    }
}
