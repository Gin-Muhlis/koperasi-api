<?php

namespace App\Http\Controllers;
use App\Repositories\Member\MemberRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

require_once app_path() . '/Helpers/helpers.php';

use App\Http\Requests\StoreReceivableRequest;
use App\Repositories\Loan\LoanRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReceivableController extends Controller
{
    private $loanRepo;
    private $memberRepo;

    public function __construct(LoanRepository $loanRepo, MemberRepository $memberRepository) {
        $this->loanRepo = $loanRepo;
        $this->memberRepo = $memberRepository;
    }
    public function store(StoreReceivableRequest $request) 
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();
            foreach ($validated['members'] as $member) {
                $is_loan = $this->memberRepo->getNotPaidMember($member['id']);

                if (count($is_loan) > 0) {
                    return response()->json([
                        'message' => 'Terdapat member yang masih mempunyai pinjaman yang belum lunas'
                    ], 400);
                }

                $data = [
                    'uuid' => Str::uuid(),
                    'code' => generateCode(),
                    'member_id' => $member['id'],
                    'amount' => $member['amount'],
                    'sub_category_id' => $validated['sub_category_id'],
                    'user_id' => Auth::user()->id,
                    'date' => $member['date'],
                    'loan_duration' => $member['duration'],
                    'loan_interest' => 1.5,
                    'total_payment' => $member['total'],
                    'status' => 'belum bayar',
                    'deadline' => $member['deadline'],
                    'description' => $validated['description'],
                ];

                $this->loanRepo->createLoanMember($data);
            }
            DB::commit();

            return response()->json([
                'message' => 'Data peminjaman berhasil ditambahkan'
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return errorResponse($e->getMessage());
        }

    }
}
