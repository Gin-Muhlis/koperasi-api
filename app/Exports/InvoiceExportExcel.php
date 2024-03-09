<?php

namespace App\Exports;

require_once app_path() . '/Helpers/helpers.php';

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class InvoiceExportExcel implements FromView, WithTitle, WithEvents
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
		return View('exports.paymentReport', compact('data'));
	}

	public function title(): string
	{

		return $this->profile->app_name;
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

		return [
			AfterSheet::class => function (AfterSheet $event) use ($alphabets, $highest_col) {
				$event->sheet->getColumnDimension('A')->setWidth(5);
				$event->sheet->getColumnDimension('B')->setWidth(35);

				for ($i = 1; $i <= $this->total_column_data; $i++) {
					$alphabet = $alphabets[$i + 2];
					$event->sheet->getColumnDimension($alphabet)->setWidth(15);
				}

				$event->sheet->insertNewRowBefore(1, 3);

				$event->sheet->mergeCells("A1:{$highest_col}1");
				$event->sheet->mergeCells("A2:{$highest_col}2");
				$event->sheet->setCellValue('A1', strtoupper($this->profile->app_name));

				$event->sheet->getStyle('A1:A2')->getFont()->setSize(16);

				$event->sheet->setCellValue('A2', 'Daftar Potongan Koperasi' . ' ' . $this->dataExport['time']);
				$event->sheet->getStyle('A1:A2')->getFont()->setBold(true);

				$event->sheet->getStyle("A4:{$highest_col}4")->getAlignment()->setWrapText(true);

				$event->sheet->getStyle('A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
				$event->sheet->getStyle('A')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
				
				$event->sheet->getStyle("A1:{$highest_col}2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
				$event->sheet->getStyle("A1:{$highest_col}2")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

				$event->sheet->getStyle("A4:{$highest_col}" . $event->sheet->getHighestRow())->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
				$event->sheet->getStyle("A4:{$highest_col}" . $event->sheet->getHighestRow())->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

				$sign_col = $this->total_column_data - 2;

				$sign_place = $alphabets[$sign_col] . $event->sheet->getHighestRow() + 2;
				$sign_role = $alphabets[$sign_col]  . $event->sheet->getHighestRow() + 3;
				$sign_people = $alphabets[$sign_col] . $event->sheet->getHighestRow() + 7;

				$event->sheet->setCellValue($sign_place, "Cianjur {$this->dataExport['time']}");
				$event->sheet->setCellValue($sign_role, "Bendahara");
				$event->sheet->setCellValue($sign_people, $this->profile->treasurer_name);

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

	public function headings(): array
    {
		$sub_categories = [];

		foreach($this->sub_categories as $category) {
			$sub_categories[] = $category->name;
		}

		$result = [
			'No',
			'Nama',
			... $sub_categories,
			'Jumlah'
		];

        return $result;
    }
}
