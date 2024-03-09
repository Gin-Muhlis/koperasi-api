<?php

namespace App\Http\Controllers;
use App\Http\Resources\SubCategoryResource;

require_once app_path() . '/Helpers/helpers.php';

use App\Http\Requests\StoreDetailInvoiceRequest;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Http\Resources\DetailInvoiceResource;
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

class InvoiceController extends Controller {
	private $invoiceRepo;
	private $savingRepo;
	private $installmentRepo;
	private $loanRepo;
	private $subCategoryRepo;
	private $memberRepo;

	public function __construct(MemberRepository $memberRepository, SavingRepository $savingRepository, SubCategoryRepository $subCategoryRepository, InstallmentRepository $installmentRepository, LoanRepository $loanRepository, InvoiceRepository $invoiceRepository) {
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
	public function index() {
		try {
			$invoices = $this->invoiceRepo->getInvoices();

			return response()->json([
				'data' => InvoiceResource::collection($invoices),
			]);
		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}

	public function getSubCategoriesInvoice() {
		try {
			$sub_categories = $this->subCategoryRepo->getSubCategories();

			$filtered_sub_categories = [];
			foreach ($sub_categories as $sub_category) {
				if ($sub_category->category->name == 'simpanan' || $sub_category->category->name == 'piutang') {
					$filtered_sub_categories[] = $sub_category;
				}
			}

			usort($filtered_sub_categories, function ($a, $b) {
				return $a['id'] - $b['id'];
			});

            return response()->json([
                'data' => SubCategoryResource::collection($filtered_sub_categories)
            ]);
		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}

	public function getMemberInvoice() {
		try {

			$sub_categories = $this->subCategoryRepo->getSubCategories();
			$members = $this->memberRepo->getMembers();

			$filtered_sub_categories = [];
			foreach ($sub_categories as $sub_category) {
				if ($sub_category->category->name == 'simpanan' || $sub_category->category->name == 'piutang') {
					$filtered_sub_categories[] = $sub_category;
				}
			}

			$members_data = $members->map(function($member) use ($sub_categories, $filtered_sub_categories) {
				$data_dinamis = [];

				foreach ($filtered_sub_categories as $sub_category) {
					$detail = [];
					$months_saving = [];
					$months_isntallment = [];

					// simpanan
					foreach ($member->savings as $saving) {
						if ($sub_category->id == $saving->sub_category_id) {
							$months_saving[] = [
								'month_year' => $saving->month_year,
								'status' => $saving->status,
							];
						}

						if ($saving->sub_category_id == $sub_category->id) {
							$detail = [
								'amount' => $saving->amount,
								'sub_category_id' => $sub_category->id,
								'type_payment' => $saving->SubCategory->type_payment,
								'months_status' => $months_saving
							];
						}
					}

					// pinjaman
					foreach ($member->loans as $loan) {
						if ($loan->status != 'lunas' && $loan->sub_category_id == $sub_category->id) {

							foreach ($loan->installments as $installment) {
								$months_isntallment[] = [
									'month_year' => Carbon::parse($installment->date)->format('m-Y'),
									'status' => $installment->status,
								];
							}

							$detail = [
								'amount' => $loan->amount,
								'sub_category_id' => $sub_category->id,
								'loan_id'=> $loan->id,
								'total_payment' => $loan->total_payment,
								'paid' => $this->handlePaid($loan->installments),
								'remain_payment' => $loan->total_payment - $this->handlePaid($loan->installments),
								'monthly' => ceil($loan->total_payment / $loan->loan_duration / 1000) * 1000,
								'months_status' => $months_isntallment
							];
						}
					}

					$data_dinamis[$sub_category->name] = $detail;
				}

				return [
					'id' => $member->id,
					'name' => $member->name,
					'position' => $member->position,
					'position_category_id' => $member->group_id,
					'data' => $data_dinamis
				];
			});

			return response()->json([
				'data' => $members_data
			]);

		}catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}

	
	private function handlePaid($data) {
		if (count($data) < 1) {
			return 0;
		}

		$totalPaid = 0;
		foreach ($data as $item) {
			$totalPaid += $item->amount;
		}
		return $totalPaid;
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(StoreInvoiceRequest $request) {
		try {
			$validated = $request->validated();

			$year_now = Carbon::now()->format('Y');

			$last_invoice = $this->invoiceRepo->getLastInvoice($year_now);

			$code = '';

			if (is_null($last_invoice)) {
				$code = Carbon::now()->format('Y') . '0001';
			} else {
				$last_code = $last_invoice->invoice_code;
				$date_part = substr($last_code,0,4);
				$number_part = intval(substr($last_code,4)) + 1;

				$code = $date_part . str_pad($number_part,4,'0', STR_PAD_LEFT);
			}

			$data = [
				...$validated,
				'invoice_code' => $code,
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

	// get detail invoice
	public function detailInvoice($code) 
	{
		try {
			$detailInvoice = $this->invoiceRepo->getDetailInvoiceByCode($code);

			return response()->json([
				'data' => new DetailInvoiceResource($detailInvoice),
			]);
		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}

	/**
	 * store detial invoice
	 */

	 public function storeDetailInvoice(StoreDetailInvoiceRequest $request) {
		try {
			$validated = $request->validated();

			$sub_categories = $this->subCategoryRepo->getSubCategories();

			$filtered_sub_categories = [];
			foreach ($sub_categories as $sub_category) {
				if ($sub_category->category->name == 'simpanan' || $sub_category->category->name == 'piutang') {
					$filtered_sub_categories[] = $sub_category;
				}
			}

			DB::beginTransaction();

			foreach ($validated['members'] as $member) {
				foreach ($filtered_sub_categories as $sub_category) {
					$time = $validated['month_year'];

					if ($sub_category->category->name == 'simpanan') {

						if (array_key_exists($sub_category->name, $member)) {
							$sub_category_data = $member[$sub_category->name];
								// cek apakah ada anggota yang sudah membayar simpanan pada bulan yang ditentukan
								if ($sub_category->type_payment == 'monthly') {
									$is_already_saving = $this->savingRepo->getMemberSpesificSavings($member['id'], $sub_category->id);

									if (count($is_already_saving) > 0 && $is_already_saving->contains('month_year', $time)) {
										return response()->json([
											'message' => "Terdapat anggota yang sudah membayar {$sub_category->name} pada bulan yang ditentukan",
										], 400);
									}
								}

								$data = $this->generateSavingData($member['id'], $sub_category_data['amount'], $sub_category->id, $validated['description'], $time, $validated['invoice_id']);

								$this->savingRepo->makeSavingMembers($data);
						}
						
					}

					if ($sub_category->category->name == 'piutang') {
						if (array_key_exists($sub_category->name, $member)) {
							$sub_category_data = $member[$sub_category->name];
							$loan_id = $sub_category_data['loanId'];
							// cek apakah ada anggota yang sudah membayar piutang pada bulan yang ditentukan
							if ($sub_category->type_payment == 'monthly') {
								$is_already_payed_isntallment = $this->installmentRepo->getMemberPaymentMonth($time[1], $time[0], $loan_id);
								if (count($is_already_payed_isntallment) > 0) {
									return response()->json([
										'message' => "Terdapat anggota yang sudah membayar {$sub_category->name} pada bulan yang ditentukan",
									], 400);
								}
							}
							$data = $this->generateInstallmentData($member['id'], $loan_id, $sub_category_data['amount'] , $sub_category->id, $validated['invoice_id']);

							$this->installmentRepo->makeInstallmentMembers($data);
							$loan_member = $this->loanRepo->findLoan($loan_id);

							$total_payment_member = $this->installmentRepo->getSumPayment($loan_member->id);

							if ($total_payment_member >= $loan_member->total_payment) {
								$this->loanRepo->updateStatusLoan($loan_member->id, [...$loan_member->toArray(), 'status' => 'lunas']);
							} else {
								$this->loanRepo->updateStatusLoan($loan_member->id, [...$loan_member->toArray(), 'status' => 'berjalan']);
							}
						}
					}
				}
			}

			DB::commit();

			return response()->json([
				'message' => 'Data Invoice berhasil ditambahkan'
			]);
		} catch (Exception $e) {
			DB::rollback();
			return errorResponse($e->getMessage());
		}
	 }

	/**
	 * Display the specified resource.
	 */
	public function show(Invoice $invoice) {
		//
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(UpdateInvoiceRequest $request, Invoice $invoice) {
		//
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Invoice $invoice) {
		//
	}

	private function generateSavingData($member_id, $amount, $sub_category, $description, $month_year, $invoice_id) {
		return [
			'uuid' => Str::uuid(),
			'code' => generateCode(),
			'member_id' => $member_id,
			'amount' => $amount,
			'date' => Carbon::now()->format('Y-m-d'),
			'sub_category_id' => $sub_category,
			'month_year' => $month_year,
			'user_id' => Auth::user()->id,
			'invoice_id' => $invoice_id,
			'description' => $description,
		];
	}

	private function generateInstallmentData($member_id, $loan_id, $amount, $sub_category, $invoice_id) {
		return [
			'uuid' => Str::uuid(),
			'code' => generateCode(),
			'loan_id' => $loan_id,
			'member_id' => $member_id,
			'amount' => $amount,
			'date' => Carbon::now()->format('Y-m-d'),
			'sub_category_id' => $sub_category,
			'user_id' => Auth::user()->id,
			'invoice_id' => $invoice_id,
		];
	}
}
