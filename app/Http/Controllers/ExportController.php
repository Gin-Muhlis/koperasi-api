<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExportInvoiceMemberRequest;
use App\Repositories\Invoice\InvoiceRepository;
use App\Repositories\Member\MemberRepository;
use App\Repositories\SubCategory\SubCategoryRepository;
use Carbon\Carbon;

require_once app_path() . '/Helpers/helpers.php';

use App\Exports\InvoiceExportExcel;
use App\Http\Requests\DownloadInvoiceRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
	private $invoiceRepo;
	private $subCategoryRepo;
	private $memberRepo;

	public function __construct(InvoiceRepository $invoiceRepository, SubCategoryRepository $subCategoryRepository, MemberRepository $memberRepository)
	{
		$this->invoiceRepo = $invoiceRepository;
		$this->subCategoryRepo = $subCategoryRepository;
		$this->memberRepo = $memberRepository;
	}
	public function detailInvoiceExportExcel($invoice_code)
	{
		try {
			$data = $this->generateDataExport($invoice_code);

			return Excel::download(new InvoiceExportExcel($data), "Pembyaran Koperasi {$data['time_invoice']}.xlsx");
		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}

	public function detailInvoiceExportPdf($invoice_code)
	{
		try {

			$data = $this->generateDataExport($invoice_code);

			$pdf = Pdf::loadView('pdf.invoice', compact('data'))->setPaper('a4', 'landscape');

			return $pdf->download("Invoice Zie Koperasi.pdf");
		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}

	public function invoiceMember(ExportInvoiceMemberRequest $request)
	{
		try {
			$validated = $request->validated();

			$sub_categories = $this->subCategoryRepo->getSubCategories();
			$detail_invoice = $this->invoiceRepo->getDetailInvoiceByCode($validated['invoice_code']);

			$filtered_sub_categories = [];
			foreach ($sub_categories as $sub_category) {
				if ($sub_category->category->name == 'simpanan' || $sub_category->category->name == 'piutang') {
					$filtered_sub_categories[] = $sub_category;
				}
			}

			usort($filtered_sub_categories, function ($a, $b) {
				return $a['id'] - $b['id'];
			});

			$result = [];

			$member = $this->memberRepo->find($validated['member_id']);
			$data_dinamis = [];
			foreach ($filtered_sub_categories as $sub_category) {
				if ($sub_category->category->name == 'simpanan') {
					// saving
					$saving_member = $detail_invoice->savings->where('sub_category_id', $sub_category->id)->where('member_id', $member->id)->first();

					// $sub_category_name = str_replace(' ', '_', $sub_category->name);

					$data_dinamis[$sub_category->name] = $saving_member?->amount ?? 0;
				} else {
					// installment
					$installment_member = $detail_invoice->installments->where('sub_category_id', $sub_category->id)->where('member_id', $member->id)->first();

					$data_dinamis[$sub_category->name] = $installment_member?->amount ?? 0;
				}
			}

			$invoice_date = generateDate($detail_invoice->date);

			$month_split = explode(' ', $invoice_date);

			$result = [
				'member_name' => $member->name,
				'invoice_month' => $month_split[1],
				'now' => generateDate(Carbon::now()->format('Y-m-d')),
				'sub_categories' => $filtered_sub_categories,
				...$data_dinamis
			];

			$pdf = Pdf::loadView('pdf.invoice-member', compact('result'))->setPaper('a4');
			return $pdf->download("zie_koperasi.pdf");

		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}

	private function generateDataExport($invoice_code) {
		$sub_categories = $this->subCategoryRepo->getSubCategories();
		$detail_invoice = $this->invoiceRepo->getDetailInvoiceByCode($invoice_code);
		$filtered_sub_categories = [];
		foreach ($sub_categories as $sub_category) {
			if ($sub_category->category->name == 'simpanan' || $sub_category->category->name == 'piutang') {
				$filtered_sub_categories[] = $sub_category;
			}
		}

		usort($filtered_sub_categories, function ($a, $b) {
			return $a['id'] - $b['id'];
		});

		$result = [];
		$members_id = [];

		foreach ($detail_invoice->savings as $saving) {
			if (!in_array($saving->member_id, $members_id)) {
				$members_id[] = $saving->member_id;
			}
		}

		foreach ($detail_invoice->installments as $installment) {
			if (!in_array($installment->member_id, $members_id)) {
				$members_id[] = $installment->loan->member_id;
			}
		}

		foreach ($members_id as $id) {
			$member = $this->memberRepo->find($id);
			$data_dinamis = [];
			foreach ($filtered_sub_categories as $sub_category) {
				if ($sub_category->category->name == 'simpanan') {
					// saving
					$saving_member = $detail_invoice->savings->where('sub_category_id', $sub_category->id)->where('member_id', $id)->first();

					$data_dinamis[$sub_category->name] = $saving_member?->amount ?? 0;
				} else {
					// installment
					$installment_member = $detail_invoice->installments->where('sub_category_id', $sub_category->id)->where('member_id', $id)->first();

					$data_dinamis[$sub_category->name] = $installment_member?->amount ?? 0;
				}

			}

			$result[] = [
				'member_id' => $member->id,
				'member_name' => $member->name,
				'total_row' => array_sum($data_dinamis),
				...$data_dinamis
			];
		}

		usort($result, function ($a, $b) {
			return $a['member_id'] - $b['member_id'];
		});

		$total_cols = [];

		foreach ($filtered_sub_categories as $sub_category) {
			$total_col = 0;
			foreach ($result as $row) {
				$total_cols[$sub_category->name] = $total_col += $row[$sub_category->name];
			}
		}

		$total_invoice = 0;
		foreach ($total_cols as $total) {
			$total_invoice += $total;
		}

		$invoice_date = generateDate($detail_invoice->date);

		$month_split = explode(' ', $invoice_date);

		$data = [
			'rows' => $result,
			'total_cols' => $total_cols,
			'total_invoice' => $total_invoice,
			'sub_categories' => $filtered_sub_categories,
			'time_invoice' => $month_split[1]
		];

		return $data;
	}
}
