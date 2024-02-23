<?php

namespace App\Http\Controllers;

require_once app_path() . '/Helpers/helpers.php';

use App\Exports\InvoiceExportExcel;
use App\Http\Requests\DownloadInvoiceRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller {
	public function detailInvoiceExportExcel(DownloadInvoiceRequest $request) {
		try {
			$validated = $request->validated();

			return Excel::download(new InvoiceExportExcel($validated['data'], $validated['time_invoice']), "Pembyaran Koperasi {$validated['time_invoice']}.xlsx");
		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}

	public function detailInvoiceExportPdf(DownloadInvoiceRequest $request) {
		try {
			$validated = $request->validated();
			$data_invoice = $validated['data'];
			$row_data = [];
			$total_principal_saving = 0;
			$total_mandatory_saving = 0;
			$total_special_mandatory_saving = 0;
			$total_voluntary_saving = 0;
			$total_recretional_saving = 0;
			$total_receivable = 0;
			$total_account_receivable = 0;
			$total_invoice = 0;

			$split_time = explode(' ', $validated['time_invoice']);

			$validated['time_invoice'] = $split_time[1] . ' ' . $split_time[2];

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

			$data = [
				'row_data' => $row_data,
				'total_principal_saving' => $total_principal_saving,
				'total_mandatory_saving' => $total_mandatory_saving,
				'total_special_mandatory_saving' => $total_special_mandatory_saving,
				'total_voluntary_saving' => $total_voluntary_saving,
				'total_recretional_saving' => $total_recretional_saving,
				'total_receivable' => $total_receivable,
				'total_account_receivable' => $total_account_receivable,
				'total_invoice' => $total_invoice,
				'time_invoice' => $validated['time_invoice'],
			];

			$pdf = Pdf::loadView('pdf.invoice', compact('data'))->setPaper('a4', 'landscape');
			return $pdf->download("Pembayaran Invoice {$validated['time_invoice']}.pdf");
		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}
}
