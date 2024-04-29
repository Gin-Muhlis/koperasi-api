<?php

namespace App\Http\Controllers;

require_once app_path() . '/Helpers/helpers.php';

use App\Repositories\Member\MemberRepository;
use App\Repositories\SubCategory\SubCategoryRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreReceivableRequest;
use App\Http\Resources\SubCategoryResource;
use App\Repositories\Loan\LoanRepository;
use Exception;
use Illuminate\Support\Str;

class ReceivableController extends Controller
{
    private $loanRepo;
    private $memberRepo;
    private $subCategoryRepo;


    public function __construct(LoanRepository $loanRepo, MemberRepository $memberRepository, SubCategoryRepository $subCategoryRepository)
    {
        $this->loanRepo = $loanRepo;
        $this->memberRepo = $memberRepository;
        $this->subCategoryRepo = $subCategoryRepository;
    }

    public function index()
    {
        try {
            $sub_categories = $this->subCategoryRepo->getSubCategories();
            $members = $this->memberRepo->getMembers();

            $filtered_sub_categories = filterLoanCategories($sub_categories);

            $members_data = $members->map(function ($member) use ($filtered_sub_categories) {
                $data_dinamis = [];

                // pinjaman
                $total = 0;
                $loan_progress = false;


                foreach ($member->loans as $loan) {
                    $total += $loan->total_payment;
                    if ($loan->status != 'lunas') {
                        $loan_progress = true;
                    }

                    $detail = [
                        'code' => $loan->code,
                        'sub_category' => $loan->subCategory->name,
                        'total_loan' => $loan->total_payment,
                        'paid' => handlePaid($loan->installments),
                        'remain_payment' => $loan->total_payment - handlePaid($loan->installments),
                        'duration' => $loan->loan_duration,
                        'deadline' => $loan->deadline->toDateString(),
                        'date_completion' => $loan->date_completion ? $loan->date_completion->toDateString() : null,
                        'status' => $loan->status,
                    ];
                    $data_dinamis[] = $detail;
                }

                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'position' => $member->position,
                    'total_loan' => $total,
                    'loan_progress' => $loan_progress,
                    'detail_loans' => $data_dinamis
                ];
            });

            return response()->json([
                'data' => $members_data
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    public function store(StoreReceivableRequest $request)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $data = [
                'uuid' => Str::uuid(),
                'code' => generateCode(),
                'member_id' => $validated['member_id'],
                'amount' => $validated['amount'],
                'sub_category_id' => $validated['sub_category_id'],
                'user_id' => Auth::user()->id,
                'date' => $validated['date'],
                'loan_duration' => $validated['duration'],
                'loan_interest' => 1.5,
                'total_payment' => $validated['total'],
                'status' => 'belum bayar',
                'deadline' => $validated['deadline'],
                'description' => $validated['description'],
            ];

            $this->loanRepo->createLoanMember($data);

            DB::commit();

            return response()->json([
                'message' => 'Data peminjaman berhasil ditambahkan'
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return errorResponse($e->getMessage());
        }

    }

    public function getSubCategories()
    {
        try {
            $sub_categories = $this->subCategoryRepo->getSubCategories();

            $filtered_sub_categories = filterLoanCategories($sub_categories);

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

}
