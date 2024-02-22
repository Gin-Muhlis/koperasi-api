<?php

namespace App\Http\Controllers;

require_once app_path() . '/Helpers/helpers.php';

use App\Exports\InvoiceExportExcel;
use App\Http\Requests\DownloadInvoiceRequest;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller {
	public function detailInvoiceExportExcel(DownloadInvoiceRequest $request) {
		try {
			$validated = $request->validated();

			return Excel::download(new InvoiceExportExcel($validated['data']), 'tagihan_gabungan.xlsx');
		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}
}
