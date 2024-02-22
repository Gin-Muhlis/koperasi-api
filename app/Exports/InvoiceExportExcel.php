<?php

namespace App\Exports;

require_once app_path() .  '/Helpers/helpers.php';

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class InvoiceExportExcel implements FromView, WithTitle, WithEvents
{

    private $dataInvoice;

    public function __construct($dataInvoice) {
        $this->dataInvoice = $dataInvoice;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
	{
        $data_invoice = $this->dataInvoice;

        
		
		return View('exports.paymentReport', compact('data_invoice'));
	}

	public function title(): string
	{
        $now = Carbon::now()->format('Y-m-d');

        $formatedDate = generateDate($now);
		return "Pembayara $formatedDate";
	}

	public function registerEvents(): array
	{
		return [
			AfterSheet::class => function (AfterSheet $event) {
				$event->sheet->getColumnDimension('A')->setAutoSize(true);
				$event->sheet->getColumnDimension('B')->setAutoSize(true);
				$event->sheet->getColumnDimension('C')->setAutoSize(true);
				$event->sheet->getColumnDimension('D')->setAutoSize(true);
				$event->sheet->getColumnDimension('E')->setAutoSize(true);
				$event->sheet->getColumnDimension('F')->setAutoSize(true);
				$event->sheet->getColumnDimension('G')->setAutoSize(true);
				$event->sheet->getColumnDimension('H')->setAutoSize(true);

				$event->sheet->insertNewRowBefore(1, 3);
				$event->sheet->mergeCells('A1:H1');
				$event->sheet->mergeCells('A2:H2');
				$event->sheet->setCellValue('A1', 'DATA TAGIHAN GABUNGAN');

				$event->sheet->getStyle('A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
				$event->sheet->getStyle('A1:H2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
				$event->sheet->getStyle('A4:H4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

				$event->sheet->getStyle('A4:H4')->applyFromArray([
					'fill' => [
						'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
						'startColor' => [
							'argb' => '10A3EF',
						],
					],
				]);
			},
		];
	}
}
