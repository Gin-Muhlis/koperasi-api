<?php

namespace App\Http\Controllers;
use App\Repositories\Installment\InstallmentRepository;
use App\Repositories\Loan\LoanRepository;
use App\Repositories\Member\MemberRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

require_once app_path() . '/Helpers/helpers.php';

use App\Models\Installment;
use App\Http\Requests\StoreInstallmentRequest;
use App\Http\Requests\UpdateInstallmentRequest;

class InstallmentController extends Controller
{
    private $installmentRepo;
	private $loanRepo;
	private $memberRepo;

	public function __construct(InstallmentRepository $installmentRepository, LoanRepository $loanRepository, MemberRepository $memberRepository) {
		$this->installmentRepo = $installmentRepository;
		$this->loanRepo = $loanRepository;
        $this->memberRepo = $memberRepository;
	}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $members = $this->memberRepo->getNotPaidMembers();

			$members_data = $members->map(function($member) {
					$detail = [];
					$months_isntallment = [];

					

					// pinjaman
					foreach ($member->loans as $loan) {
						if ($loan->status != 'lunas') {

							foreach ($loan->installments as $installment) {
								$months_isntallment[] = [
									'month_year' => Carbon::parse($installment->date)->format('m-Y'),
									'status' => $installment->status,
								];
							}

							$detail = [
								'sub_category_id' => $loan->subCategory->id,
								'loan_id'=> $loan->id,
                                'loan_date' => $loan->date,
								'total_payment' => $loan->total_payment,
								'paid' => $this->handlePaid($loan->installments),
								'remain_payment' => $loan->total_payment - $this->handlePaid($loan->installments),
								'monthly' => ceil($loan->total_payment / $loan->loan_duration / 1000) * 1000,
                                'duration' => $loan->loan_duration,
                                'remain_duration' => $loan->loan_duration - count($months_isntallment),
								'months_status' => $months_isntallment
							];
						}
					}

                    

				return [
					'id' => $member->id,
					'name' => $member->name,
					...$detail
				];
			});

			return response()->json([
				'data' => $members_data
			]);

        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInstallmentRequest $request)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

				$data = $this->generateInstallmentData($validated);

				$this->installmentRepo->makeInstallmentMembers($data);

				$loan_member = $this->loanRepo->findLoan($validated['loan_id']);

				$total_payment_member = $this->installmentRepo->getSumPayment($loan_member->id);

				if ($total_payment_member >= $loan_member->total_payment) {
					$this->loanRepo->updateStatusLoan($loan_member->id, [...$loan_member->toArray(), 'status' => 'lunas']);
				} else {
					$this->loanRepo->updateStatusLoan($loan_member->id, [...$loan_member->toArray(), 'status' => 'berjalan']);
				}
			

			DB::commit();
			return response()->json([
				'message' => 'Pembayaran berhasil ditambahkan',
			]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Installment $installment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInstallmentRequest $request, Installment $installment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Installment $installment)
    {
        //
    }
    private function generateInstallmentData($data) {
		return [
			'uuid' => Str::uuid(),
			'code' => generateCode(),
            'member_id' => $data['member_id'],
			'loan_id' => $data['loan_id'],
			'amount' => $data['amount'],
			'date' => Carbon::now()->format('Y-m-d'),
			'sub_category_id' => $data['sub_category_id'],
			'user_id' => Auth::user()->id,
		];
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

}
