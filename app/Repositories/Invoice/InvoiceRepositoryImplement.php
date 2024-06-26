<?php

namespace App\Repositories\Invoice;

use App\Models\Invoice;
use LaravelEasyRepository\Implementations\Eloquent;

class InvoiceRepositoryImplement extends Eloquent implements InvoiceRepository {

	
	protected $model;

	public function __construct(Invoice $model) {
		$this->model = $model;
	}

	public function getInvoices() {
		return $this->model->latest()->get();
	}

	public function createInvoice($data) {
		return $this->create($data);
	}


	public function getDetailInvoiceByCode($code) {
		return $this->model->with(['savings', 'installments', 'user'])->where('invoice_code', $code)->first();
	}

	public function updateStatusInvoice($id) {
		$this->model->find($id)->update(['status' => 'dibayar']);
	}

	public function getNotPaidInvoices() {
		return $this->model->where('status', 'belum bayar')->count();
	}

	public function getPaidInvoices() {
		return $this->model->where('status', 'dibayar')->count();
	}

	public function getHistoryInvoices() {
		return $this->model->limit(4)->select('invoice_name', 'date', 'due_date', 'status', 'payment_source')->latest()->get();
	}

	public function getLastInvoice($year) {

		return $this->model->whereYear('date', '=', $year)->latest()->first();
	}
}
