<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExportInvoiceMemberRequest;
use App\Repositories\Installment\InstallmentRepository;
use App\Repositories\Invoice\InvoiceRepository;
use App\Repositories\Loan\LoanRepository;
use App\Repositories\Member\MemberRepository;
use App\Repositories\ProfileApp\ProfileAppRepository;
use App\Repositories\Saving\SavingRepository;
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
	private $profileRepo;
	private $savingRepo;
	private $loanRepo;

	public function __construct(InvoiceRepository $invoiceRepository, SubCategoryRepository $subCategoryRepository, MemberRepository $memberRepository, ProfileAppRepository $profileAppRepository, SavingRepository $savingRepository, LoanRepository $loanRepository)
	{
		$this->invoiceRepo = $invoiceRepository;
		$this->subCategoryRepo = $subCategoryRepository;
		$this->memberRepo = $memberRepository;
		$this->profileRepo = $profileAppRepository;
		$this->savingRepo = $savingRepository;
		$this->loanRepo = $loanRepository;
	}
	public function detailInvoiceExportExcel($invoice_code)
	{
		try {
			$data = $this->generateDataExport($invoice_code);
			$sub_categories = $this->subCategoryRepo->getSubCategories();
			$profile = $this->profileRepo->getProfile();

			$filtered_sub_categories = [];
			foreach ($sub_categories as $sub_category) {
				if ($sub_category->category->name == 'simpanan' || $sub_category->category->name == 'piutang') {
					$filtered_sub_categories[] = $sub_category;
				}
			}

			return Excel::download(new InvoiceExportExcel($data, $filtered_sub_categories, $profile), "Koperasi.xlsx");
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
			$profile_app = $this->profileRepo->getProfile();

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
			$total_sub_categories = [];
			foreach ($filtered_sub_categories as $sub_category) {
				if ($sub_category->category->name == 'simpanan') {
					// saving
					$saving_member = $detail_invoice->savings->where('sub_category_id', $sub_category->id)->where('member_id', $member->id)->first();

					// $sub_category_name = str_replace(' ', '_', $sub_category->name);

					$data_dinamis[$sub_category->name] = $saving_member?->amount ?? 0;

					$total_saving = $this->savingRepo->getTotalSavingBySubCategory($sub_category->id, $member->id);
					$total_sub_categories[$sub_category->name] = $total_saving;
				} else {
					// installment
					$installment_member = $detail_invoice->installments->where('sub_category_id', $sub_category->id)->where('member_id', $member->id)->first();

					$data_dinamis[$sub_category->name] = $installment_member?->amount ?? 0;
					$total_installment = $this->loanRepo->getTotalLoanBySubCategory($sub_category->id, $member->id);
					$total_sub_categories[$sub_category->name] = $total_installment;
				}
			}

			$total = 0;
			$total_balance = 0;

			foreach($data_dinamis as $data => $value) {
				$total += $value;
			}

			foreach($total_sub_categories as $data => $value) {
				$total_balance += $value;
			}

			$invoice_date = generateDate($detail_invoice->date);

			$month_split = explode(' ', $invoice_date);

			$result = [
				'member_name' => $member->name,
				'invoice_month' => $month_split[1],
				'now' => generateDate(Carbon::now()->format('Y-m-d')),
				'profile' => $profile_app,
				'sub_categories' => $filtered_sub_categories,
				...$data_dinamis,
				'total_sub_categories' => $total_sub_categories,
				'total' => $total,
				'total_balance' => $total_balance
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
		$profile_app = $this->profileRepo->getProfile();

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

		$date_split = explode(' ', $invoice_date);

		$now = Carbon::now()->format('Y-m-d');

		$data = [
			'rows' => $result,
			'total_cols' => $total_cols,
			'total_invoice' => $total_invoice,
			'sub_categories' => $filtered_sub_categories,
			'profile' => $profile_app,
			'time' => $date_split[1] . ' ' . $date_split[2],
			'now' => generateDate($now)
		];

		return $data;
	}
}
