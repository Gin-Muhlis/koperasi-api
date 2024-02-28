<?php

namespace App\Http\Controllers;
use App\Repositories\Installment\InstallmentRepository;
use App\Repositories\Loan\LoanRepository;
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
	private $subCategoryRepo;
	private $memberRepo;

	public function __construct(InstallmentRepository $installmentRepository, LoanRepository $loanRepository) {
		$this->installmentRepo = $installmentRepository;
		$this->loanRepo = $loanRepository;
	}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInstallmentRequest $request)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

			foreach ($validated['members'] as $item) {

				$month = explode('-', $validated['month_year'])[0];
				$year = explode('-', $validated['month_year'])[1];

				$is_month_payed = $this->installmentRepo->getMemberPaymentMonth($year, $month, $item['loanId']);

				if (count($is_month_payed) > 0) {
					return response()->json([
						'message' => 'Terdapat data member yang sudah membayar angsuran pada bulan yang ditentukan',
					], 400);
				}

				$data = $this->generateInstallmentData($item);

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
			'loan_id' => $data['loanId'],
			'amount' => $data['amount'],
			'date' => Carbon::now()->format('Y-m-d'),
			'sub_category_id' => $data['sub_category_id'],
			'user_id' => Auth::user()->id,
		];
	}
}
