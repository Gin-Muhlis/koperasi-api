<?php

namespace App\Http\Controllers;

require_once app_path() . '/Helpers/helpers.php';

use App\Http\Requests\StorePaymentDeterminationRequest;
use App\Http\Requests\UpdatePaymentDeterminationRequest;
use App\Models\PaymentDetermination;
use App\Repositories\PaymentDetermination\PaymentDeterminationRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentDeterminationController extends Controller {
	private $repository;

	public function __construct(PaymentDeterminationRepository $repo) {
		$this->repository = $repo;
	}
	/**
	 * Display a listing of the resource.
	 */
	public function index() {
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(StorePaymentDeterminationRequest $request) {
		try {
			$validated = $request->validated();

			foreach ($validated['members_id'] as $member_id) {
				$data = [
					'uuid' => Str::uuid(),
					'member_id' => $member_id,
					'sub_category_id' => $validated['sub_category_id'],
					'amount' => $validated['amount'],
					'payment_month' => $validated['payment_month'],
				];

				DB::beginTransaction();

				$this->repository->createPaymentDetermination($data);

				DB::commit();
			}

			return response()->json([
				'success' => true,
				'message' => 'Penetapan pembayaran berhasil ditambahkan',
			]);
		} catch (Exception $e) {
			DB::rollBack();

			return errorResponse($e->getMessage());
		}
	}

	/**
	 * Display the specified resource.
	 */
	public function show(PaymentDetermination $paymentDeteminations) {
		//
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(UpdatePaymentDeterminationRequest $request, PaymentDetermination $paymentDeteminations) {
		//
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(PaymentDetermination $paymentDeteminations) {
		//
	}

}
