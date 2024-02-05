<?php

namespace App\Exports;

use App\Models\Member;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReportPaymentExport implements FromView, WithTitle, WithEvents {
	private $principal_savings;
	private $mandatory_savings;
	private $special_mandatory_savings;
	private $voluntary_savings;
	private $recretional_savings;

	private function __construc($principal_savings, $mandatory_savings, $special_mandatory_savings, $voluntary_savings, $recretional_savings) {
		$this->principal_savings = $principal_savings;
		$this->mandatory_savings = $mandatory_savings;
		$this->special_mandatory_savings = $special_mandatory_savings;
		$this->voluntary_savings = $voluntary_savings;
		$this->recretional_savings = $recretional_savings;
	}
	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function view(): View {
		$members = Member::all();

		$total_principal_saving = 0;
		$total_mandatory_saving = 0;
		$total_special_mandatory_saving = 0;
		$total_voluntary_saving = 0;
		$total_recretional_saving = 0;
		$total_payment = 0;

		$members_data = [];

		foreach ($members as $member) {
			$principal_saving = collect($this->principal_savings)->where('id', $member->id)->first();
			$mandatory_saving = collect($this->mandatory_savings)->where('id', $member->id)->first();
			$special_mandatory_saving = collect($this->special_mandatory_savings)->where('id', $member->id)->first();
			$voluntary_saving = collect($this->voluntary_savings)->where('id', $member->id)->first();
			$recretional_saving = collect($this->recretional_savings)->where('id', $member->id)->first();
			$total_payment_member = ($principal_saving ? $principal_saving['amount'] : 0) + ($mandatory_saving ? $mandatory_saving['amount'] : 0) + ($special_mandatory_saving ? $special_mandatory_saving['amount'] : 0) + ($voluntary_saving ? $voluntary_saving['amount'] : 0) + ($recretional_saving ? $recretional_saving['amount'] : 0);

			$members_data[] = [
				'name' => $member->name,
				'principal_saving' => $principal_saving ? $principal_saving['amount'] : 0,
				'mandatory_saving' => $mandatory_saving ? $mandatory_saving['amount'] : 0,
				'special_mandatory_saving' => $special_mandatory_saving ? $special_mandatory_saving['amount'] : 0,
				'voluntary_saving' => $voluntary_saving ? $voluntary_saving['amount'] : 0,
				'recretional_saving' => $recretional_saving ? $recretional_saving['amount'] : 0,
				'total_payment_member' => $total_payment_member,
			];
		}

		foreach ($this->principal_savings as $data) {
			$total_principal_saving += $data['amount'];
		}

		foreach ($this->mandatory_savings as $data) {
			$total_mandatory_saving += $data['amount'];
		}

		foreach ($this->special_mandatory_savings as $data) {
			$total_special_mandatory_saving += $data['amount'];
		}

		foreach ($this->voluntary_savings as $data) {
			$total_voluntary_saving += $data['amount'];
		}

		foreach ($this->recretional_savings as $data) {
			$recretional_saving += $data['amount'];
		}

		$total_payment = $total_principal_saving + $total_mandatory_saving + $total_special_mandatory_saving + $total_voluntary_saving + $total_recretional_saving;

		return view('exports.paymentReport', compact('members_data', 'total_principal_saving', 'total_mandatory_saving', 'total_special_mandatory_saving', 'total_voluntary_saving', 'total_recretional_saving', 'total_payment'));
	}

	public function title(): string {
		return 'Data Tagihan Gabungan';
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

				$event->sheet->insertNewRowBefore(1, 3);
				$event->sheet->mergeCells('A1:H1');
				$event->sheet->mergeCells('A2:H2');
				$event->sheet->setCellValue('A1', 'DATA TAGIHAN GABUNGAN');

				$event->sheet->getStyle('A1:D2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
				$event->sheet->getStyle('A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
				$event->sheet->getStyle('E')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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
