<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\PaymentDetermination;
use App\Http\Requests\StorePaymentDeterminationRequest;
use App\Http\Requests\UpdatePaymentDeterminationRequest;
use App\Repositories\PaymentDetermination\PaymentDeterminationRepository;

class PaymentDeterminationController extends Controller
{
    private $repository;
    /**
     * Display a listing of the resource.
     */
    public function index(PaymentDeterminationRepository $repo)
    {
        $this->repository = $repo;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaymentDeterminationRequest $request)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            foreach ($validated['members_id'] as $member_id) {
                $data = [
                    'uuid' => Str::uuid(),
                    'member_id' => $member_id,
                    'sub_category_id' => $validated['sub_category_id'],
                    'amount' => $validated['amount'],
                    'payment_month' => $validated['payment_month']
                ];

                $this->repository->createPaymentDetermination($data);

            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Penetapan pembayaran berhasil ditambahkan'
            ]);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentDetermination $paymentDeteminations)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaymentDeterminationRequest $request, PaymentDetermination $paymentDeteminations)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentDetermination $paymentDeteminations)
    {
        //
    }

    private function errorResponse($error)
    {
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan dengan sistem',
            'error' => $error,
        ], 500);
    }
}
