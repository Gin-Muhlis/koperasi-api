<?php

namespace App\Http\Controllers;
use Exception;

require_once app_path() . '/Helpers/helpers.php';

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ProductResource;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Repositories\Product\ProductRepository;

class ProductController extends Controller
{
    private $productRepo;

    public function __construct(ProductRepository $productRepository) {
        $this->productRepo = $productRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $products = $this->productRepo->getProducts();

            return response()->json([
                'data' => ProductResource::collection($products)
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['uuid'] = Str::uuid();

            DB::beginTransaction();

            $this->productRepo->createProduct($validated);

            DB::commit();

            return response()->json([
                'message' => 'Data produk berhasil ditambahkan'
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
            $product = $this->productRepo->showProduct($id);

            return response()->json([
                'data' => new ProductResource($product)
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, $id)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $this->productRepo->updateProduct($id, $validated);

            DB::commit();

            return response()->json([
                'message' => 'Data produk berhasil diperbarui'
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

            $this->productRepo->deleteProduct($id);

            DB::commit();

            return response()->json([
                'message' => 'Data produk berhasil dihapus'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return errorResponse($e->getMessage());
        }
    }
}
