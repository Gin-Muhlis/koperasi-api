<?php

namespace App\Repositories\Invoice;

use LaravelEasyRepository\Repository;

interface InvoiceRepository extends Repository {

	public function getInvoices();
	public function createInvoice($data);
	public function getMemberInvoice($member_id, $month_year);
	public function getDetailInvoiceByCode($code);
	public function updateStatusInvoice($id);
	public function getNotPaidInvoices();
	public function getPaidInvoices();

	public function getHistoryInvoices();
	public function getLastInvoice($year);
}

