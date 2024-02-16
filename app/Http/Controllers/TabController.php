<?php

namespace App\Http\Controllers;

require_once app_path() . '/Helpers/helpers.php';

use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Resources\MandatoryResource;
use App\Http\Resources\PrincipalSavingResource;
use App\Http\Resources\ReceivableResource;
use App\Repositories\Installment\InstallmentRepository;
use App\Repositories\Invoice\InvoiceRepository;
use App\Repositories\Loan\LoanRepository;
use App\Repositories\Member\MemberRepository;
use App\Repositories\Saving\SavingRepository;
use App\Repositories\SubCategory\SubCategoryRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TabController extends Controller {
	private $memberRepo;
	private $savingRepo;
	private $installmentRepo;
	private $loanRepo;
	private $subCategoryRepo;
	private $invoiceRepo;

	public function __construct(MemberRepository $memberRepository, SavingRepository $savingRepository, SubCategoryRepository $subCategoryRepository, InstallmentRepository $installmentRepository, LoanRepository $loanRepository, InvoiceRepository $invoiceRepository) {
		$this->memberRepo = $memberRepository;
		$this->savingRepo = $savingRepository;
		$this->subCategoryRepo = $subCategoryRepository;
		$this->installmentRepo = $installmentRepository;
		$this->loanRepo = $loanRepository;
		$this->invoiceRepo = $invoiceRepository;
	}

	public function memberPrincipal() {
		try {
			$sub_category = $this->subCategoryRepo->getByName('simpanan pokok');
			$members = $this->memberRepo->getMembers();

			$member_principaL_saving = [];
			$filtered_members = $this->filterMember($members);
			foreach ($filtered_members as $member) {
				$member_savings = $this->savingRepo->getMemberSpesificSavings($member->id, $sub_category->id);

				if (count($member_savings) < 1) {
					$member_principaL_saving[] = $member;
				}
			}

			return response()->json([
				'data' => PrincipalSavingResource::collection($member_principaL_saving),
			]);
		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}

	public function mandatorySaving() {
		try {
			$members = $this->memberRepo->getMembers();

			$filtered_members = $this->filterMember($members);

			return response()->json([
				'data' => MandatoryResource::collection($filtered_members),
			]);
		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}

	public function receivable() {
		try {
			$sub_category = $this->subCategoryRepo->getByName('piutang s/p');
			$members = $this->memberRepo->getNotPaidMembers($sub_category->id);

			$filtered_members = $this->filterMember($members);

			return response()->json([
				'data' => ReceivableResource::collection($filtered_members),
			]);
		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}

	public function accountsReceivable() {
		try {
			$sub_category = $this->subCategoryRepo->getByName('piutang dagang');
			$members = $this->memberRepo->getNotPaidMembers($sub_category->id);

			$filtered_members = $this->filterMember($members);

			return response()->json([
				'data' => ReceivableResource::collection($filtered_members),
			]);
		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}

	private function filterMember($data) {
		$filtered_members = [];

		foreach ($data as $member) {
			if ($member->user->hasRole('member')) {
				$filtered_members[] = $member;
			}
		}

		return $filtered_members;
	}

	// function untuk menghandle semua jenis simpanan sekaligus
	public function storeDataInvoice(StoreInvoiceRequest $request) {
		try {
			$validated = $request->validated();

			DB::beginTransaction();

			// invoice
			foreach ($validated['invoices'] as $invoice) {
				$data = [
					'uuid' => Str::uuid(),
					'member_id' => $invoice['memberId'],
					'user_id' => Auth::user()->id,
					'principal_saving' => $invoice['principalSaving'],
					'mandatory_saving' => $invoice['mandatorySaving'],
					'special_mandatory_saving' => $invoice['specialMandatorySaving'],
					'voluntary_saving' => $invoice['voluntarySaving'],
					'recretional_saving' => $invoice['recretionalSaving'],
					'receivable' => $invoice['receivable'],
					'account_receivable' => $invoice['accountReceivable'],
					'month_year' => $validated['month_year'],
					'status' => 'belum bayar',
				];

				$this->invoiceRepo->createInvoice($data);
			}

			// simpanan pokok
			foreach ($validated['principal_savings'] as $item) {
				$sub_category = $this->subCategoryRepo->getByName('simpanan pokok');
				$invoice = $this->invoiceRepo->getMemberInvoice($item['id'], $validated['month_year']);
				$is_principal_saving = $this->savingRepo->getMemberSpesificSavings($item['id'], $sub_category->id);

				if (count($is_principal_saving) > 0) {
					return response()->json([
						'message' => 'Terdapat data member yang sudah membayar simpanan pokok',
					], 400);
				}

				$data = $this->generateSavingData($item, $sub_category->id, $validated['description'], $validated['month_year'], $invoice->id);

				$this->savingRepo->makeSavingMembers($data);
			}

			// simpanan wajib
			foreach ($validated['mandatory_savings'] as $item) {
				$sub_category = $this->subCategoryRepo->getByName('simpanan wajib');
				$invoice = $this->invoiceRepo->getMemberInvoice($item['id'], $validated['month_year']);
				$is_mandatory_saving = $this->savingRepo->getMemberSpesificSavings($item['id'], $sub_category->id);

				if (count($is_mandatory_saving) > 0 && $is_mandatory_saving->contains('month_year', $validated['month_year'])) {
					return response()->json([
						'message' => 'Terdapat data member yang sudah membayar simpanan wajib pada bulan yang ditentukan',
					], 400);
				}

				$data = $this->generateSavingData($item, $sub_category->id, $validated['description'], $validated['month_year'], $invoice->id);

				$this->savingRepo->makeSavingMembers($data);
			}

			// simpanan wajib khusus
			foreach ($validated['special_mandatory_savings'] as $item) {
				$sub_category = $this->subCategoryRepo->getByName('simpanan wajib khusus');
				$invoice = $this->invoiceRepo->getMemberInvoice($item['id'], $validated['month_year']);

				$is_mandatory_saving = $this->savingRepo->getMemberSpesificSavings($item['id'], $sub_category->id);

				if (count($is_mandatory_saving) > 0 && $is_mandatory_saving->contains('month_year', $validated['month_year'])) {
					return response()->json([
						'message' => 'Terdapat data member yang sudah membayar simpanan wajib khusus pada bulan yang ditentukan',
					], 400);
				}

				$data = $this->generateSavingData($item, $sub_category->id, $validated['description'], $validated['month_year'], $invoice->id);

				$this->savingRepo->makeSavingMembers($data);
			}

			// simpanan sukarela
			foreach ($validated['voluntary_savings'] as $item) {
				$sub_category = $this->subCategoryRepo->getByName('simpanan sukarela');
				$invoice = $this->invoiceRepo->getMemberInvoice($item['id'], $validated['month_year']);

				$data = $this->generateSavingData($item, $sub_category->id, $validated['description'], $validated['month_year'], $invoice->id);

				$this->savingRepo->makeSavingMembers($data);
			}

			// tabungan rekreasi
			foreach ($validated['recretional_savings'] as $item) {
				$sub_category = $this->subCategoryRepo->getByName('tabungan rekreasi');
				$invoice = $this->invoiceRepo->getMemberInvoice($item['id'], $validated['month_year']);

				$data = $this->generateSavingData($item, $sub_category->id, $validated['description'], $validated['month_year'], $invoice->id);

				$this->savingRepo->makeSavingMembers($data);
			}

			// piutang s/p
			foreach ($validated['receivables'] as $item) {
				$sub_category = $this->subCategoryRepo->getByName('piutang s/p');
				$invoice = $this->invoiceRepo->getMemberInvoice($item['id'], $validated['month_year']);

				$month = explode('-', $validated['month_year'])[0];

				$is_month_payed = $this->installmentRepo->getMemberPaymentMonth($month, $item['loanId']);

				if (count($is_month_payed) > 0) {
					return response()->json([
						'message' => 'Terdapat data member yang sudah membayar piutang s/p pada bulan yang ditentukan',
					], 400);
				}

				$data = $this->generateInstallmentData($item, $sub_category->id, $invoice->id);

				$this->installmentRepo->makeInstallmentMembers($data);

				$loan_member = $this->loanRepo->findLoan($item['loanId']);

				$total_payment_member = $this->installmentRepo->getSumPayment($loan_member->id);

				if ($total_payment_member >= $loan_member->total_payment) {
					$this->loanRepo->updateStatusLoan($loan_member->id, [...$loan_member->toArray(), 'status' => 'lunas']);
				} else {
					$this->loanRepo->updateStatusLoan($loan_member->id, [...$loan_member->toArray(), 'status' => 'berjalan']);
				}

			}

			// piutang dagang
			foreach ($validated['accounts_receivable'] as $item) {
				$sub_category = $this->subCategoryRepo->getByName('piutang dagang');
				$invoice = $this->invoiceRepo->getMemberInvoice($item['id'], $validated['month_year']);

				$month = explode('-', $validated['month_year'])[0];

				$is_month_payed = $this->installmentRepo->getMemberPaymentMonth($month, $item['loanId']);

				if (count($is_month_payed) > 0) {
					return response()->json([
						'message' => 'Terdapat data member yang sudah membayar piutang dagang pada bulan yang ditentukan',
					], 400);
				}

				$data = $this->generateInstallmentData($item, $sub_category->id, $invoice->id);

				$this->installmentRepo->makeInstallmentMembers($data);

				$loan_member = $this->loanRepo->findLoan($item['loanId']);

				$total_payment_member = $this->installmentRepo->getSumPayment($loan_member->id);

				if ($total_payment_member >= $loan_member->total_payment) {
					$this->loanRepo->updateStatusLoan($loan_member->id, [...$loan_member->toArray(), 'status' => 'lunas']);
				} else {
					$this->loanRepo->updateStatusLoan($loan_member->id, [...$loan_member->toArray(), 'status' => 'berjalan']);
				}
			}

			DB::commit();
			return response()->json([
				'message' => 'Data berhasil ditambahkan',
			]);
		} catch (Exception $e) {
			DB::rollback();
			return errorResponse($e->getMessage());
		}
	}

	private function generateSavingData($data, $sub_category, $description, $month_year, $invoice_id) {
		return [
			'uuid' => Str::uuid(),
			'code' => generateCode(),
			'member_id' => $data['id'],
			'amount' => $data['amount'],
			'date' => Carbon::now()->format('Y-m-d'),
			'sub_category_id' => $sub_category,
			'month_year' => $month_year,
			'user_id' => Auth::user()->id,
			'invoice_id' => $invoice_id,
			'description' => $description,
		];
	}

	private function generateInstallmentData($data, $sub_category, $invoice_id) {
		return [
			'uuid' => Str::uuid(),
			'code' => generateCode(),
			'loan_id' => $data['loanId'],
			'amount' => $data['amount'],
			'date' => Carbon::now()->format('Y-m-d'),
			'sub_category_id' => $sub_category,
			'user_id' => Auth::user()->id,
			'invoice_id' => $invoice_id,
		];
	}
}
