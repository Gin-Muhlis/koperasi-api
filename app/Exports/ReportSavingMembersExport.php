<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ReportSavingMembersExport implements FromView, WithTitle, WithEvents
{
    private $dataExport;
	private $sub_categories;
	private $profile;

	private $total_column_data;

	public function __construct($data, $sub_categories, $profile)
	{
		$this->dataExport = $data;
		$this->sub_categories = $sub_categories;
		$this->profile = $profile;
		$this->total_column_data = count($sub_categories) + 3;
	}
	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function view(): View
	{

		$data = $this->dataExport;
        $sub_categories_saving = $this->sub_categories;
		

        $total_cols = [];

		foreach ($this->sub_categories as $sub_category) {
			$total_col = 0;
			
			foreach ($data as $row) {
				$total_cols[$sub_category->name] = $total_col += $row[$sub_category->name];

			}
		}

        $total_col_saving = 0;
        foreach ($sub_categories_saving as $sub_category) {
			foreach ($data as $row) {
				$total_cols['total_col_saving'] = $total_col_saving += $row[$sub_category->name];

			}
		}


    return View('exports.reportSavingMembers', compact('data', 'sub_categories_saving', 'total_cols'));
	}

	public function title(): string
	{

		return 'Zie Koperasi';
	}

	public function registerEvents(): array
	{

		$alphabets = [
			1 => 'A',
			2 => 'B',
			3 => 'C',
			4 => 'D',
			5 => 'E',
			6 => 'F',
			7 => 'G',
			8 => 'H',
			9 => 'I',
			10 => 'J',
			11 => 'K',
			12 => 'L',
			13 => 'M',
			14 => 'N',
			15 => 'O',
			16 => 'P',
			17 => 'Q',
			18 => 'R',
			19 => 'S',
			20 => 'T',
			21 => 'U',
			22 => 'V',
			23 => 'W',
			24 => 'X',
			25 => 'Y',
			26 => 'Z'
		];

		$highest_col = $alphabets[$this->total_column_data];
        $now = Carbon::now()->format('Y-m-d');
        $now_string = generateDate($now);

		return [
			AfterSheet::class => function (AfterSheet $event) use ($alphabets, $highest_col, $now_string) {
				$event->sheet->getColumnDimension('A')->setWidth(5);
				$event->sheet->getColumnDimension('B')->setWidth(40);

				for ($i = 1; $i <= $this->total_column_data; $i++) {
					$alphabet = $alphabets[$i + 2];
					$event->sheet->getColumnDimension($alphabet)->setWidth(15);
				}

				$event->sheet->insertNewRowBefore(1, 3);

				$event->sheet->mergeCells("A1:{$highest_col}1");
				$event->sheet->mergeCells("A2:{$highest_col}2");
				$event->sheet->setCellValue('A1', strtoupper('Laporan Simpanan Anggota Koperasi'));

				$event->sheet->getStyle('A1:A2')->getFont()->setSize(16);

				$event->sheet->setCellValue('A2', "Per " . strtoupper($now_string));
				$event->sheet->getStyle('A1:A2')->getFont()->setBold(true);

				$event->sheet->getStyle("A4:{$highest_col}4")->getAlignment()->setWrapText(true);

				$event->sheet->getStyle('A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
				$event->sheet->getStyle('A')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
				
				$event->sheet->getStyle("A1:{$highest_col}2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
				$event->sheet->getStyle("A1:{$highest_col}2")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

				$event->sheet->getStyle("A4:{$highest_col}" . $event->sheet->getHighestRow())->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
				$event->sheet->getStyle("A4:{$highest_col}" . $event->sheet->getHighestRow())->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                $gap = floor($this->total_column_data / 2);

                $chairman_col = 2;
				$secretary_col = $this->total_column_data - $gap;
				$treasurer_col = $this->total_column_data - 1;

                
				$time_place = $alphabets[$treasurer_col] . $event->sheet->getHighestRow() + 2;

                $chairman = $alphabets[$chairman_col] . $event->sheet->getHighestRow() + 3;
                $sign_chairman = $alphabets[$chairman_col] . $event->sheet->getHighestRow() + 7;

                $secretary = $alphabets[$secretary_col] . $event->sheet->getHighestRow() + 3;
                $sign_secretary = $alphabets[$secretary_col] . $event->sheet->getHighestRow() + 7;

				$treasurer = $alphabets[$treasurer_col]  . $event->sheet->getHighestRow() + 3;
				$sign_treasurer = $alphabets[$treasurer_col] . $event->sheet->getHighestRow() + 7;

				$event->sheet->setCellValue($time_place, "Cianjur, {$now_string}");

				$event->sheet->setCellValue($chairman, "Ketua");
				$event->sheet->setCellValue($sign_chairman, $this->profile->chairmans_name);

                $event->sheet->setCellValue($secretary, "Sekretaris");
				$event->sheet->setCellValue($sign_secretary, $this->profile->secretary_name);

				$event->sheet->setCellValue($treasurer, "Bendahara");
				$event->sheet->setCellValue($sign_treasurer, $this->profile->treasurer_name);

				$event->sheet->getStyle("A4:{$highest_col}" . $event->sheet->getHighestRow() - 7)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ]
                ]);

			},
		];
	}
}
