<?php

namespace App\Http\Controllers;

require_once app_path() . '/Helpers/helpers.php';

use Exception;
use Carbon\Carbon;
use App\Models\Sale;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\SaleResource;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Repositories\Sale\SaleRepository;
use App\Repositories\SalesDetail\SalesDetailRepository;

class SaleController extends Controller
{
    private $saleRepo;
    private $salesDetailRepo;

    public function __construct(SaleRepository $saleRepository, SalesDetailRepository $salesDetailRepository) {
        $this->saleRepo = $saleRepository;
        $this->salesDetailRepo = $salesDetailRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $sales = Sale::all();

            return response()->json([
                'data' => SaleResource::collection($sales)
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSaleRequest $request)
    {
        try {
            $user = $request->user();

            $validated = $request->validated();
            $validated['uuid'] = Str::uuid();
            $validated['code'] = generateCode();
            $validated['date'] = Carbon::now()->format('Y-m-d');
            $validated['user_id'] = $user->id;

            DB::beginTransaction();

            $sale = $this->saleRepo->createSale($validated);

            foreach($validated['stuffs'] as $stuff) {
                $data = [
                    'uuid' => Str::uuid(),
                    'sale_id' => $sale->id,
                    'qty' => $stuff['qty'],
                    'unit_price' => $stuff['unit_price'],
                    'sub_total' => $stuff['sub_total'],
                    'stuff_id' => $stuff['id']
                ];

                $this->salesDetailRepo->createSalesDetail($data);
            }

            DB::commit();

            return response()->json([
                'message' => 'Data penjualan berhasil ditambahkan'
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
            $sale = $this->saleRepo->showSale($id);

            return response()->json([
                'data' => new SaleResource($sale)
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSaleRequest $request, $id)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $this->saleRepo->updateSale($id, $validated);

            DB::commit();

            return response()->json([
                'message' => 'Data penjualan berhasil diperbarui'
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

            $this->saleRepo->deleteSale($id);

            DB::commit();

            return response()->json([
                'message' => 'Data penjualan berhasil dihapus'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return errorResponse($e->getMessage());
        }
    }
}
