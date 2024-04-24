<?php

namespace App\Http\Controllers;

require_once app_path() . '/Helpers/helpers.php';

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StorePaymentRequest;
use App\Repositories\Saving\SavingRepository;
use App\Repositories\Invoice\InvoiceRepository;
use App\Repositories\Payment\PaymentRepository;
use App\Repositories\Installment\InstallmentRepository;

class PaymentController extends Controller {
	private $paymentRepo;
	private $invoiceRepo;
	private $savingRepo;
	private $installmentRepo;

	public function __construct(PaymentRepository $paymentRepository, InvoiceRepository $invoiceRepository, SavingRepository $savingRepository, InstallmentRepository $installmentRepository
	) {
		$this->paymentRepo = $paymentRepository;
		$this->invoiceRepo = $invoiceRepository;
		$this->savingRepo = $savingRepository;
		$this->installmentRepo = $installmentRepository;
	}
	
	public function store(StorePaymentRequest $request) {
		try {
			$validated = $request->validated();

			if ($validated['amount'] !== $validated['total_invoice']) {
				return response()->json([
					'message' => 'Jumlah Pembayaran tidak sesuai dengan total invoice yang harus dibayar',
				], 400);
			}

			DB::beginTransaction();

			$data = [
				...$validated,
				'uuid' => Str::uuid(),
				'date_payment' => Carbon::now()->format('Y-m-d'),
			];

			$this->paymentRepo->createPayment($data);
		
			$this->invoiceRepo->updateStatusInvoice($validated['invoice_id']);
			
			$savingsInvoice = $this->savingRepo->getSavingByInvoiceId($validated['invoice_id']);
			
			foreach ($savingsInvoice as $saving) {
				$this->savingRepo->updateStatusSaving($saving->id);
			}

			$installmentsInvoice = $this->installmentRepo->getInstalmentByInvoiceId($validated['invoice_id']);
			
			foreach ($installmentsInvoice as $installment) {
				$this->installmentRepo->updateStatusIsntallment($installment->id);
			}

			DB::commit();

			return response()->json([
				'message' => 'Pembayaran invoice berhasil ditambahkan.',
			]);
		} catch (Exception $e) {
			DB::rollback();
			return errorResponse($e->getMessage());
		}
	}

}
