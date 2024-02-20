<?php

namespace App\Repositories\Invoice;

use App\Models\Invoice;
use LaravelEasyRepository\Implementations\Eloquent;

class InvoiceRepositoryImplement extends Eloquent implements InvoiceRepository {

	/**
	 * Model class to be used in this repository for the common methods inside Eloquent
	 * Don't remove or change $this->model variable name
	 * @property Model|mixed $model;
	 */
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

	public function getMemberInvoice($member_id, $month_year) {
		return $this->model->where([
			['member_id', $member_id],
			['month_year', $month_year],
			['status', 'belum bayar'],
		])->first();
	}

	public function getDetailInvoiceByCode($code) {
		return $this->model->with(['savings', 'installments', 'user'])->where('invoice_code', $code)->first();
	}
}
