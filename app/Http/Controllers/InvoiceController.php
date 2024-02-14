<?php

namespace App\Http\Controllers;

require_once app_path() . '/Helpers/helpers.php';

use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Repositories\Invoice\InvoiceRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceController extends Controller {
	private $invoiceRepo;

	public function __construct(InvoiceRepository $invoiceRepository) {
		$this->invoiceRepo = $invoiceRepository;
	}
	/**
	 * Display a listing of the resource.
	 */
	public function index() {
		try {
			$invoices = $this->invoiceRepo->getInvoices();

			return response()->json([
				'data' => InvoiceResource::collection($invoices),
			]);
		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}

	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(StoreInvoiceRequest $request) {
		try {
			$validated = $request->validated();

			$validated['due_date'] = Carbon::parse($validated['due_date'])->format('Y-m-d');

			$data = [
				...$validated,
				'invoice_code' => Str::random(10),
				'date' => Carbon::now()->format('Y-m-d'),
				'status' => 'belum bayar',
				'user_id' => Auth::user()->id,
			];

			DB::beginTransaction();

			$invoice = $this->invoiceRepo->createInvoice($data);

			DB::commit();

			return response()->json([
				'invoice' => $invoice,
				'message' => 'Invoice berhasil ditambahkan',
			]);
		} catch (Exception $e) {
			DB::rollback();
			return errorResponse($e->getMessage());
		}
	}

	/**
	 * Display the specified resource.
	 */
	public function show(Invoice $invoice) {
		//
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(UpdateInvoiceRequest $request, Invoice $invoice) {
		//
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Invoice $invoice) {
		//
	}
}
