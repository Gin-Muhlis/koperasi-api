<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MembersDataExport implements FromView, WithTitle, WithEvents
{
    private $data;

	public function __construct($data)
	{
		$this->data = $data;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        $data = $this->data;

        return view('exports.membersData', compact(('data')));
    }

    public function title(): string
	{
		return 'Zie Koperasi';
	}

    public function registerEvents(): array
	{

		return [
			AfterSheet::class => function (AfterSheet $event)  {
				$event->sheet->getColumnDimension('A')->setWidth(5);
				$event->sheet->getColumnDimension('B')->setWidth(40);
				$event->sheet->getColumnDimension('C')->setWidth(40);

				$event->sheet->insertNewRowBefore(1, 2);

				$event->sheet->mergeCells("A1:C1");
				$event->sheet->mergeCells("A2:C2");
				$event->sheet->setCellValue('A1', strtoupper('Daftar Anggota Koperasi'));

				$event->sheet->getStyle('A1:A2')->getFont()->setSize(16);

				$event->sheet->getStyle('A1:A2')->getFont()->setBold(true);
                $event->sheet->getStyle("A1:C1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);


				$event->sheet->getStyle("A3:C" . $event->sheet->getHighestRow())->applyFromArray([
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
