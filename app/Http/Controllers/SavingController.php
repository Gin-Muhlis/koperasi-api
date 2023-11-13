<?php

namespace App\Http\Controllers;

require_once app_path() . 'Helpers/helpers.php';

use Exception;
use Carbon\Carbon;
use App\Models\Member;
use App\Models\Saving;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreSavingRequest;
use App\Http\Requests\UpdateSavingRequest;
use App\Repositories\Member\MemberRepository;
use App\Repositories\Saving\SavingRepository;
use App\Repositories\PaymentDetermination\PaymentDeterminationRepository;

class SavingController extends Controller
{
    private $savingRepository;
    private $memberRepository;

    private $paymentDeterminationRepository;

    public function __construct(SavingRepository $savingRepository, MemberRepository $memberRepository, PaymentDeterminationRepository $paymentDeterminationRepository)
    {
        $this->savingRepository = $savingRepository;
        $this->memberRepository = $memberRepository;
        $this->paymentDeterminationRepository = $paymentDeterminationRepository;
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
    public function store(StoreSavingRequest $request)
    {
        try {
            $validated = $request->validated();


            foreach ($validated['members_id'] as $member_id) {
                $member = $this->memberRepository->showMember($member_id);

                $memberPayment = $this->paymentDeterminationRepository->memberPayment($member->id, $validated['month_year']);
                
                $data = [
                    'uuid' => Str::uuid(),
                    'code' => generateCode(),
                    'member_id' => $member->id,
                    'amount' => $memberPayment->amount,
                    'date' => Carbon::now()->format('Y-m-d'),
                    'sub_category_id' => $validated['sub_category_id'],
                    'month_year' => $validated['month_year'],
                    'user_id' => '1',
                    'description' => $validated['description']
                ];

                DB::beginTransaction();

                $this->savingRepository->makeSavingMembers($data);

                DB::commit();
            }

            return response()->json([
                'success' => true,
                'message' => 'Simpanan member berhasil ditambahkan'
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return errorResponse($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Saving $saving)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSavingRequest $request, Saving $saving)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Saving $saving)
    {
        //
    }

    
}
