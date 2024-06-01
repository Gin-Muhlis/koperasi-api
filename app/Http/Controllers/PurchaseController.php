<?php

namespace App\Http\Controllers;
use Exception;
use Carbon\Carbon;
use App\Models\Purchase;

require_once app_path() . '/Helpers/helpers.php';

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\PurchaseResource;
use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Repositories\Purchase\PurchaseRepository;
use App\Repositories\PurchaseDetail\PurchaseDetailRepository;

class PurchaseController extends Controller
{
    private $purchaseRepo;
    private $purchaseDetailRepo;

    public function __construct(PurchaseRepository $purchaseRepository, PurchaseDetailRepository $purchaseDetailRepository) {
        $this->purchaseRepo = $purchaseRepository;
        $this->purchaseDetailRepo = $purchaseDetailRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $purchases = Purchase::all();

            return response()->json([
                'data' => PurchaseResource::collection($purchases)
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePurchaseRequest $request)
    {
        try {
            $user = $request->user();

            $validated = $request->validated();
            $validated['uuid'] = Str::uuid();
            $validated['date_purchase'] = Carbon::now()->format('Y-m-d');
            $validated['user_id'] = $user->id;

            DB::beginTransaction();

            $purchase = $this->purchaseRepo->createPurchase($validated);

            foreach($validated['stuffs'] as $stuff) {
                $data = [
                    'uuid' => Str::uuid(),
                    'purchase_id' => $purchase->id,
                    'qty' => $stuff['qty'],
                    'unit_price' => $stuff['unit_price'],
                    'sub_total' => $stuff['sub_total'],
                    'stuff_id' => $stuff['id']
                ];

                $this->purchaseDetailRepo->createPurchaseDetail($data);
            }

            DB::commit();

            return response()->json([
                'message' => 'Data pembelian berhasil ditambahkan'
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $purchase = $this->purchaseRepo->showPurchase($id);

            return response()->json([
                'data' => new PurchaseResource($purchase)
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePurchaseRequest $request, $id)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $this->purchaseRepo->updatePurchase($id, $validated);

            DB::commit();

            return response()->json([
                'message' => 'Data pembelian berhasil diperbarui'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $this->purchaseRepo->deletePurchase($id);

            DB::commit();

            return response()->json([
                'message' => 'Data pembelian berhasil dihapus'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return errorResponse($e->getMessage());
        }
    }
}
