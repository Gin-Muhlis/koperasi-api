<?php

namespace App\Repositories\Invoice;

use LaravelEasyRepository\Repository;

interface InvoiceRepository extends Repository {

	public function getInvoices();
	public function createInvoice($data);
}
