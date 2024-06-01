<?php

namespace App\Http\Controllers;

require_once app_path() . '/Helpers/helpers.php';

use App\Repositories\Installment\InstallmentRepository;
use App\Repositories\Loan\LoanRepository;
use App\Repositories\Member\MemberRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests\StoreInstallmentRequest;

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
								'paid' => handlePaid($loan->installments),
								'remain_payment' => $loan->total_payment - handlePaid($loan->installments),
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
					$this->loanRepo->updateStatusLoan($loan_member->id, [...$loan_member->toArray(), 'status' => 'lunas', 'date_completion' => Carbon::now()->setTimezone('Asia/jakarta')->format('Y-m-d')]);
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

    private function generateInstallmentData($data) {
		return [
			'uuid' => Str::uuid(),
            'member_id' => $data['member_id'],
			'loan_id' => $data['loan_id'],
			'amount' => $data['amount'],
			'date' => Carbon::now()->format('Y-m-d'),
			'sub_category_id' => $data['sub_category_id'],
			'user_id' => Auth::user()->id,
		];
	}



}
