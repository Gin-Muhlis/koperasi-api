<?php

namespace App\Http\Controllers;

require_once app_path() . '/Helpers/helpers.php';

use App\Http\Requests\StoreDetailInvoiceRequest;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
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

class InvoiceController extends Controller
{
	private $invoiceRepo;
	private $savingRepo;
	private $installmentRepo;
	private $loanRepo;
	private $subCategoryRepo;
	private $memberRepo;

	public function __construct(MemberRepository $memberRepository, SavingRepository $savingRepository, SubCategoryRepository $subCategoryRepository, InstallmentRepository $installmentRepository, LoanRepository $loanRepository, InvoiceRepository $invoiceRepository)
	{
		$this->memberRepo = $memberRepository;
		$this->savingRepo = $savingRepository;
		$this->subCategoryRepo = $subCategoryRepository;
		$this->installmentRepo = $installmentRepository;
		$this->loanRepo = $loanRepository;
		$this->invoiceRepo = $invoiceRepository;
	}
	/**
	 * Display a listing of the resource.
	 */
	public function index()
	{
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
	public function store(StoreInvoiceRequest $request)
	{
		try {
			$validated = $request->validated();

			$data = [
				...$validated,
				'invoice_code' => Str::random(10),
				'date' => Carbon::now()->format('Y-m-d'),
				'status' => 'belum bayar',
				'user_id' => Auth::user()->id,
			];

			DB::beginTransaction();

			$invoice = $this->invoiceRepo->createInvoice($data);

			DB::commit();

			return response()->json([
				'invoice' => $invoice,
				'message' => 'Invoice berhasil ditambahkan',
			]);
		} catch (Exception $e) {
			DB::rollback();
			return errorResponse($e->getMessage());
		}
	}

	/**
	 * store detial invoice
	 */

	public function storeDetailInvoice(StoreDetailInvoiceRequest $request)
	{
		try {
			$validated = $request->validated();

			DB::beginTransaction();

			// simpanan pokok
			foreach ($validated['principal_savings'] as $item) {
				$sub_category = $this->subCategoryRepo->getByName('simpanan pokok');
				$is_principal_saving = $this->savingRepo->getMemberSpesificSavings($item['id'], $sub_category->id);

				if (count($is_principal_saving) > 0) {
					return response()->json([
						'message' => 'Terdapat data member yang sudah membayar simpanan pokok',
					], 400);
				}

				$data = $this->generateSavingData($item, $sub_category->id, $validated['description'], $validated['month_year'], $validated['invoice_id']);

				$this->savingRepo->makeSavingMembers($data);
			}

			// simpanan wajib
			foreach ($validated['mandatory_savings'] as $item) {
				$sub_category = $this->subCategoryRepo->getByName('simpanan wajib');
				$is_mandatory_saving = $this->savingRepo->getMemberSpesificSavings($item['id'], $sub_category->id);

				if (count($is_mandatory_saving) > 0 && $is_mandatory_saving->contains('month_year', $validated['month_year'])) {
					return response()->json([
						'message' => 'Terdapat data member yang sudah membayar simpanan wajib pada bulan yang ditentukan',
					], 400);
				}

				$data = $this->generateSavingData($item, $sub_category->id, $validated['description'], $validated['month_year'], $validated['invoice_id']);

				$this->savingRepo->makeSavingMembers($data);
			}

			// simpanan wajib khusus
			foreach ($validated['special_mandatory_savings'] as $item) {
				$sub_category = $this->subCategoryRepo->getByName('simpanan wajib khusus');

				$is_mandatory_saving = $this->savingRepo->getMemberSpesificSavings($item['id'], $sub_category->id);

				if (count($is_mandatory_saving) > 0 && $is_mandatory_saving->contains('month_year', $validated['month_year'])) {
					return response()->json([
						'message' => 'Terdapat data member yang sudah membayar simpanan wajib khusus pada bulan yang ditentukan',
					], 400);
				}

				$data = $this->generateSavingData($item, $sub_category->id, $validated['description'], $validated['month_year'], $validated['invoice_id']);

				$this->savingRepo->makeSavingMembers($data);
			}

			// simpanan sukarela
			foreach ($validated['voluntary_savings'] as $item) {
				$sub_category = $this->subCategoryRepo->getByName('simpanan sukarela');

				$data = $this->generateSavingData($item, $sub_category->id, $validated['description'], $validated['month_year'], $validated['invoice_id']);
				$this->savingRepo->makeSavingMembers($data);
			}

			// tabungan rekreasi
			foreach ($validated['recretional_savings'] as $item) {
				$sub_category = $this->subCategoryRepo->getByName('tabungan rekreasi');

				$data = $this->generateSavingData($item, $sub_category->id, $validated['description'], $validated['month_year'], $validated['invoice_id']);
				$this->savingRepo->makeSavingMembers($data);
			}

			// piutang s/p
			foreach ($validated['receivables'] as $item) {
				$sub_category = $this->subCategoryRepo->getByName('piutang s/p');

				$month = explode('-', $validated['month_year'])[0];

				$is_month_payed = $this->installmentRepo->getMemberPaymentMonth($month, $item['loanId']);

				if (count($is_month_payed) > 0) {
					return response()->json([
						'message' => 'Terdapat data member yang sudah membayar piutang s/p pada bulan yang ditentukan',
					], 400);
				}

				$data = $this->generateInstallmentData($item, $sub_category->id, $validated['invoice_id']);

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

				$month = explode('-', $validated['month_year'])[0];

				$is_month_payed = $this->installmentRepo->getMemberPaymentMonth($month, $item['loanId']);

				if (count($is_month_payed) > 0) {
					return response()->json([
						'message' => 'Terdapat data member yang sudah membayar piutang dagang pada bulan yang ditentukan',
					], 400);
				}

				$data = $this->generateInstallmentData($item, $sub_category->id, $validated['invoice_id']);

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

	/**
	 * Display the specified resource.
	 */
	public function show(Invoice $invoice)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(UpdateInvoiceRequest $request, Invoice $invoice)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Invoice $invoice)
	{
		//
	}

	private function generateSavingData($data, $sub_category, $description, $month_year, $invoice_id)
	{
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

	private function generateInstallmentData($data, $sub_category, $invoice_id)
	{
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
