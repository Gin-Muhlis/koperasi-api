<?php

namespace App\Http\Controllers;

require_once app_path() . '/Helpers/helpers.php';

use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Models\Payment;
use App\Repositories\Invoice\InvoiceRepository;
use App\Repositories\Payment\PaymentRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller {
	private $paymentRepo;
	private $invoiceRepo;

	public function __construct(PaymentRepository $paymentRepository, InvoiceRepository $invoiceRepository) {
		$this->paymentRepo = $paymentRepository;
		$this->invoiceRepo = $invoiceRepository;
	}
	/**
	 * Display a listing of the resource.
	 */
	public function index() {
		//
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create() {
		//
	}

	/**
	 * Store a newly created resource in storage.
	 */
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
				'date_payment' => Carbon::now()->format('Y-m-d'),
			];

			$this->paymentRepo->createPayment($data);

			$this->invoiceRepo->updateStatusInvoice($validated['invoice_id']);

			DB::commit();

			return response()->json([
				'message' => 'Pembayaran berhasil',
			]);
		} catch (Exception $e) {
			DB::rollback();
			return errorResponse($e->getMessage());
		}
	}

	/**
	 * Display the specified resource.
	 */
	public function show(Payment $payment) {
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(Payment $payment) {
		//
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(UpdatePaymentRequest $request, Payment $payment) {
		//
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Payment $payment) {
		//
	}
}
