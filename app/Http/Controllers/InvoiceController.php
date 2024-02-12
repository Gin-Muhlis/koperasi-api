<?php

namespace App\Http\Controllers;

require_once app_path() . '/Helpers/helpers.php';

use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Repositories\Invoice\InvoiceRepository;
use Exception;

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
		//
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
