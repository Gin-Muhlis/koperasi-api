<?php

namespace App\Http\Controllers;

require_once app_path() . '/Helpers/helpers.php';

use App\Exports\MembersDataExport;
use App\Exports\ReportLoanMembersExport;
use App\Exports\ReportMembersExport;
use App\Exports\ReportSavingMembersExport;
use App\Http\Requests\ExportInvoiceMemberRequest;
use App\Repositories\Installment\InstallmentRepository;
use App\Repositories\Invoice\InvoiceRepository;
use App\Repositories\Loan\LoanRepository;
use App\Repositories\Member\MemberRepository;
use App\Repositories\ProfileApp\ProfileAppRepository;
use App\Repositories\Saving\SavingRepository;
use App\Repositories\SubCategory\SubCategoryRepository;
use Carbon\Carbon;
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

			$filtered_sub_categories = filterSavingLoanCategories($sub_categories);

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

			$filtered_sub_categories = filterSavingLoanCategories($sub_categories);
			

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

			foreach ($data_dinamis as $data => $value) {
				$total += $value;
			}

			foreach ($total_sub_categories as $data => $value) {
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

	// Laporan Anggota
	public function ReportMembers()
	{
		try {
			$members = $this->memberRepo->getMembers();

			$sub_categories = $this->subCategoryRepo->getSubCategories();
			$profile = $this->profileRepo->getProfile();

			$filtered_sub_categories = filterSavingLoanCategories($sub_categories);

			$data = $members->map(function ($member) use ($filtered_sub_categories) {
				$data_dinamis = [];
				$total_saving_member = 0;
				$total_loan_member = 0;

				foreach ($member->savings as $saving) {
					$total_saving_member += $saving->amount;
				}

				// pinjaman
				foreach ($member->loans as $loan) {
					$total_loan_member += $loan->total_payment;
				}

				foreach ($filtered_sub_categories as $sub_category) {
					$detail = 0;

					$total_saving = 0;
					// simpanan
					foreach ($member->savings as $saving) {
						if ($saving->sub_category_id == $sub_category->id) {
							$total_saving += $saving->amount;
							$detail = $total_saving;
						}
					}

					$total_loan = 0;
					// pinjaman
					foreach ($member->loans as $loan) {
						if ($loan->sub_category_id == $sub_category->id) {
							$total_loan += $loan->total_payment;
							$detail = $total_loan;
						}
					}

					$data_dinamis[$sub_category->name] = $detail;
				}

				return [
					'id' => $member->id,
					'name' => $member->name,
					...$data_dinamis,
					'total_saving' => $total_saving_member,
					'total_loan' => $total_loan_member
				];
			});


			return Excel::download(new ReportMembersExport($data, $filtered_sub_categories, $profile), "Koperasi.xlsx");

		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}

	public function reportMember($id)
	{
		try {
			$months = [
				'01' => 'Januari',
				'02' => 'Februari',
				'03' => 'Maret',
				'04' => 'April',
				'05' => 'Mei',
				'06' => 'Juni',
				'07' => 'Juli',
				'08' => 'Agustus',
				'09' => 'September',
				'10' => 'Oktober',
				'11' => 'November',
				'12' => 'Desember',
			];

			$member = $this->memberRepo->showMember($id);

			$sub_categories = $this->subCategoryRepo->getSubCategories();
			$profile = $this->profileRepo->getProfile();

			$filtered_sub_categories = filterSavingLoanCategories($sub_categories);

			$total_saving_member = 0;
			$total_loan_member = 0;

			foreach ($member->savings as $saving) {
				$total_saving_member += $saving->amount;
			}

			// pinjaman
			foreach ($member->loans as $loan) {
				$total_loan_member += $loan->total_payment;
			}

			foreach ($filtered_sub_categories as $sub_category) {
				$detail = [];

				// simpanan
				foreach ($member->savings as $saving) {
					if ($saving->sub_category_id == $sub_category->id) {
						$month_year = explode('-', $saving->month_year);
						$detail[] = [
							'sub_category_id' => $sub_category->id,
							'month' => $month_year[0],
							'amount' => $saving->amount
						];
					}
				}

				// pinjaman
				foreach ($member->loans as $loan) {

					if ($loan->sub_category_id == $sub_category->id) {
						$date = explode('-', $loan->date);
						$detail[] = [
							'sub_category_id' => $sub_category->id,
							'month' => $date[1],
							'amount' => $loan->total_payment
						];
					}
				}

				$data_dinamis[$sub_category->name] = $detail;
			}

			$result_amount = [];
			$result_total = [];

			foreach ($months as $key => $value) {
				$data_sub = [];
				$data_total_sub = [];

				$total_per_month_saving = 0;
				$total_per_month_loan = 0;

				$total_col_per_month_saving = 0;
				$total_col_per_month_loan = 0;

				foreach ($filtered_sub_categories as $sub_category) {
					$amount = 0;
					$total_sub = 0;
					$list_data = $data_dinamis[$sub_category->name];
					foreach ($list_data as $data) {
						// simpanan
						if ($sub_category->category->name == 'simpanan') {
							if ($data['month'] == $key) {
								$total_per_month_saving += $data['amount'];
							}
						}
						if ($sub_category->category->name == 'piutang') {
							if ($data['month'] == $key) {
								$total_per_month_loan += $data['amount'];
							}
						}
						if ($data['month'] == $key) {
							$amount += $data['amount'];
						}


						// pinjaman
						if ($sub_category->category->name == 'simpanan') {
							if ($data['month'] <= $key) {
								$total_col_per_month_saving += $data['amount'];
							}
						}
						if ($sub_category->category->name == 'piutang') {
							if ($data['month'] <= $key) {
								$total_col_per_month_loan += $data['amount'];
							}
						}
						if ($data['month'] <= $key) {
							$total_sub += $data['amount'];
						}
					}

					$data_sub[$sub_category->name] = [
						'amount' => $amount
					];

					$data_total_sub[$sub_category->name] = [
						'amount' => $total_sub
					];
				}

				$result_amount[$key] = [
					'total_per_month_saving' => $total_per_month_saving,
					'total_per_month_loan' => $total_per_month_loan,
					...$data_sub
				];

				$result_total[$key] = [
					'total_col_per_month_saving' => $total_col_per_month_saving,
					'total_col_per_month_loan' => $total_col_per_month_loan,
					...$data_total_sub
				];
			}

			$sub_categories_saving = [];
			$sub_categories_loan = [];
			foreach ($filtered_sub_categories as $sub_category) {
				if ($sub_category->category->name == 'simpanan') {
					$sub_categories_saving[] = $sub_category;
				} else {
					$sub_categories_loan[] = $sub_category;
				}
			}

			$year_now = Carbon::now()->year;

			$data = [
				'id' => $member->id,
				'name' => $member->name,
				'position' => $member->position,
				'total_saving' => $total_saving_member,
				'total_loan' => $total_loan_member,
				'result_amount' => $result_amount,
				'result_total' => $result_total,
			];

			$pdf = Pdf::loadView('pdf.report-member', compact('data', 'profile', 'sub_categories_saving', 'sub_categories_loan', 'year_now', 'months'))->setPaper('a4', 'landscape');
			return $pdf->download("zie_koperasi.pdf");

		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}

	public function reportSavingMembers()
	{
		try {
			$members = $this->memberRepo->getMembers();

			$sub_categories = $this->subCategoryRepo->getSubCategories();
			$profile = $this->profileRepo->getProfile();

			$filtered_sub_categories = filterSavingCategories($sub_categories);

			$data = $members->map(function ($member) use ($filtered_sub_categories) {
				$data_dinamis = [];
				$total_saving_member = 0;

				foreach ($member->savings as $saving) {
					$total_saving_member += $saving->amount;
				}


				foreach ($filtered_sub_categories as $sub_category) {
					$detail = 0;

					$total_saving = 0;
					// simpanan
					foreach ($member->savings as $saving) {
						if ($saving->sub_category_id == $sub_category->id) {
							$total_saving += $saving->amount;
							$detail = $total_saving;
						}
					}
					$data_dinamis[$sub_category->name] = $detail;
				}

				return [
					'id' => $member->id,
					'name' => $member->name,
					...$data_dinamis,
					'total_saving' => $total_saving_member,
				];
			});
			return Excel::download(new ReportSavingMembersExport($data, $filtered_sub_categories, $profile), "Koperasi.xlsx");

		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}

	public function reportSavingMember($id)
	{
		try {
			$months = [
				'01' => 'Januari',
				'02' => 'Februari',
				'03' => 'Maret',
				'04' => 'April',
				'05' => 'Mei',
				'06' => 'Juni',
				'07' => 'Juli',
				'08' => 'Agustus',
				'09' => 'September',
				'10' => 'Oktober',
				'11' => 'November',
				'12' => 'Desember',
			];

			$member = $this->memberRepo->showMember($id);

			$sub_categories = $this->subCategoryRepo->getSubCategories();
			$profile = $this->profileRepo->getProfile();

			$filtered_sub_categories = filterSavingCategories($sub_categories);


			$total_saving_member = 0;
			foreach ($member->savings as $saving) {
				$total_saving_member += $saving->amount;
			}


			foreach ($filtered_sub_categories as $sub_category) {
				$detail = [];

				// simpanan
				foreach ($member->savings as $saving) {
					if ($saving->sub_category_id == $sub_category->id) {
						$month_year = explode('-', $saving->month_year);
						$detail[] = [
							'sub_category_id' => $sub_category->id,
							'month' => $month_year[0],
							'amount' => $saving->amount
						];
					}
				}

				$data_dinamis[$sub_category->name] = $detail;
			}

			$result_amount = [];
			$result_total = [];

			foreach ($months as $key => $value) {
				$data_sub = [];
				$data_total_sub = [];

				$total_per_month_saving = 0;

				$total_col_per_month_saving = 0;

				foreach ($filtered_sub_categories as $sub_category) {
					$amount = 0;
					$total_sub = 0;
					$list_data = $data_dinamis[$sub_category->name];
					foreach ($list_data as $data) {
						// simpanan
						if ($sub_category->category->name == 'simpanan') {
							if ($data['month'] == $key) {
								$total_per_month_saving += $data['amount'];
							}
						}
						if ($data['month'] == $key) {
							$amount += $data['amount'];
						}


						// pinjaman
						if ($sub_category->category->name == 'simpanan') {
							if ($data['month'] <= $key) {
								$total_col_per_month_saving += $data['amount'];
							}
						}
						if ($data['month'] <= $key) {
							$total_sub += $data['amount'];
						}
					}

					$data_sub[$sub_category->name] = [
						'amount' => $amount
					];

					$data_total_sub[$sub_category->name] = [
						'amount' => $total_sub
					];
				}

				$result_amount[$key] = [
					'total_per_month_saving' => $total_per_month_saving,
					...$data_sub
				];

				$result_total[$key] = [
					'total_col_per_month_saving' => $total_col_per_month_saving,
					...$data_total_sub
				];
			}

			$sub_categories_saving = [];
			$sub_categories_loan = [];
			foreach ($filtered_sub_categories as $sub_category) {
				if ($sub_category->category->name == 'simpanan') {
					$sub_categories_saving[] = $sub_category;
				} else {
					$sub_categories_loan[] = $sub_category;
				}
			}

			$year_now = Carbon::now()->year;

			$data = [
				'id' => $member->id,
				'name' => $member->name,
				'position' => $member->position,
				'total_saving' => $total_saving_member,
				'result_amount' => $result_amount,
				'result_total' => $result_total,
			];

			$pdf = Pdf::loadView('pdf.report-saving-member', compact('data', 'profile', 'sub_categories_saving', 'year_now', 'months'))->setPaper('a4', 'landscape');
			return $pdf->download("zie_koperasi.pdf");

		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}

	public function ReportLoanMembers()
	{
		try {
			$members = $this->memberRepo->getMembers();

			$sub_categories = $this->subCategoryRepo->getSubCategories();
			$profile = $this->profileRepo->getProfile();

			$filtered_sub_categories = filterLoanCategories($sub_categories);
			
			$data = $members->map(function ($member) use ($filtered_sub_categories) {
				$data_dinamis = [];
				$total_loan_member = 0;

				// pinjaman
				foreach ($member->loans as $loan) {
					$total_loan_member += $loan->total_payment;
				}

				foreach ($filtered_sub_categories as $sub_category) {
					$detail = 0;

					$total_loan = 0;
					// pinjaman
					foreach ($member->loans as $loan) {
						if ($loan->sub_category_id == $sub_category->id) {
							$total_loan += $loan->total_payment;
							$detail = $total_loan;
						}
					}

					$data_dinamis[$sub_category->name] = $detail;
				}

				return [
					'id' => $member->id,
					'name' => $member->name,
					...$data_dinamis,
					'total_loan' => $total_loan_member
				];
			});


			return Excel::download(new ReportLoanMembersExport($data, $filtered_sub_categories, $profile), "Koperasi.xlsx");

		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}

	public function ReportLoanMember($id)
	{
		try {
			$member = $this->memberRepo->showMember($id);

			$sub_categories = $this->subCategoryRepo->getSubCategories();
			$profile = $this->profileRepo->getProfile();

			$filtered_sub_categories = filterLoanCategories($sub_categories);

			$data_dinamis = [];
			$total_loan_member = 0;

			// pinjaman
			foreach ($member->loans as $loan) {
				$total_loan_member += $loan->total_payment;
			}

			foreach ($filtered_sub_categories as $sub_category) {
				$detail = 0;

				$total_loan = 0;
				// pinjaman
				foreach ($member->loans as $loan) {
					if ($loan->sub_category_id == $sub_category->id) {
						$total_loan += $loan->total_payment;
						$detail = $total_loan;
					}
				}

				$data_dinamis[$sub_category->name] = $detail;
			}

			$year_now = Carbon::now()->year;

			$data = [
				'id' => $member->id,
				'name' => $member->name,
				'position' => $member->position,
				...$data_dinamis,
				'total_loan' => $total_loan_member
			];


			$pdf = Pdf::loadView('pdf.report-loan-member', compact('data', 'profile', 'filtered_sub_categories', 'year_now'))->setPaper('a4');

			return $pdf->download("zie_koperasi.pdf");
		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}

	public function exportMembers() {
		try {
			$members = $this->memberRepo->all();

			return Excel::download(new MembersDataExport($members), "Koperasi.xlsx");			
		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}

	private function generateDataExport($invoice_code)
	{
		$sub_categories = $this->subCategoryRepo->getSubCategories();
		$detail_invoice = $this->invoiceRepo->getDetailInvoiceByCode($invoice_code);
		$profile_app = $this->profileRepo->getProfile();

		$filtered_sub_categories = filterSavingLoanCategories($sub_categories);

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
