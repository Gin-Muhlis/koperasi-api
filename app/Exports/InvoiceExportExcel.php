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

class InvoiceExportExcel implements FromView, WithTitle, WithEvents {

	private $dataInvoice;
	private $time_invoice;

	public function __construct($dataInvoice, $timeInvoice) {
		$this->dataInvoice = $dataInvoice;
		$split_time = explode(' ', $timeInvoice);

		$this->time_invoice = $split_time[1] . ' ' . $split_time[2];
	}
	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function view(): View {
		$data_invoice = $this->dataInvoice;
		$row_data = [];
		$total_principal_saving = 0;
		$total_mandatory_saving = 0;
		$total_special_mandatory_saving = 0;
		$total_voluntary_saving = 0;
		$total_recretional_saving = 0;
		$total_receivable = 0;
		$total_account_receivable = 0;
		$total_invoice = 0;

		foreach ($data_invoice as $invoice) {
			$total_row = intval($invoice['principalSaving']) + intval($invoice['mandatorySaving']) + intval($invoice['specialMandatorySaving']) + intval($invoice['voluntarySaving']) + intval($invoice['recretionalSaving']) + intval($invoice['receivable']) + intval($invoice['accountReceivable']);

			$row_data[] = [
				'name' => $invoice['memberName'],
				'principalSaving' => $invoice['principalSaving'],
				'mandatorySaving' => $invoice['mandatorySaving'],
				'specialMandatorySaving' => $invoice['specialMandatorySaving'],
				'voluntarySaving' => $invoice['voluntarySaving'],
				'recretionalSaving' => $invoice['recretionalSaving'],
				'receivable' => $invoice['receivable'],
				'accountReceivable' => $invoice['accountReceivable'],
				'totalRow' => $total_row,
			];

			$total_principal_saving += $invoice['principalSaving'];
			$total_mandatory_saving += $invoice['mandatorySaving'];
			$total_special_mandatory_saving += $invoice['specialMandatorySaving'];
			$total_voluntary_saving += $invoice['voluntarySaving'];
			$total_recretional_saving += $invoice['recretionalSaving'];
			$total_receivable += $invoice['receivable'];
			$total_account_receivable += $invoice['accountReceivable'];

			$total_invoice += $total_row;
		}

		return View('exports.paymentReport', compact('row_data', 'total_principal_saving', 'total_mandatory_saving', 'total_special_mandatory_saving', 'total_voluntary_saving', 'total_recretional_saving', 'total_receivable', 'total_account_receivable', 'total_invoice'));
	}

	public function title(): string {
		$now = Carbon::now()->format('Y-m-d');

		$formatedDate = generateDate($now);
		return "Pembayaran $formatedDate";
	}

	public function registerEvents(): array {

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
				$event->sheet->getColumnDimension('I')->setAutoSize(true);
				$event->sheet->getColumnDimension('J')->setAutoSize(true);

				$event->sheet->insertNewRowBefore(1, 3);
				$event->sheet->mergeCells('A1:J1');
				$event->sheet->mergeCells('A2:J2');
				$event->sheet->setCellValue('A1', 'Pembayaran Koperasi' . ' ' . $this->time_invoice);

				$event->sheet->getStyle('A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
				$event->sheet->getStyle('A1:J2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
				$event->sheet->getStyle('A4:J4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

				$event->sheet->getStyle('A4:J4')->applyFromArray([
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
