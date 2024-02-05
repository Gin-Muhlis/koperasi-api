<?php

namespace App\Http\Controllers;

require_once app_path() . '/Helpers/helpers.php';

use App\Exports\ReportPaymentExport;
use App\Http\Requests\ReportPaymentRequest;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function reportPayment(ReportPaymentRequest $request)
    {
        try {
            $validated = $request->validated();
            return Excel::download(new ReportPaymentExport($validated['principal_savings'], $validated['mandatory_savings'], $validated['special_mandatory_savings'], $validated['voluntary_savings'], $validated['recretional_savings']), 'tagihan_gabungan.xlsx');
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }
}
