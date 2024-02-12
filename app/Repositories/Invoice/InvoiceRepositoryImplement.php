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
		return $this->all();
	}

	public function createInvoice($data) {
		$this->create($data);
	}
}
