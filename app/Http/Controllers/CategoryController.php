<?php

namespace App\Http\Controllers;

require_once app_path() . '/Helpers/helpers.php';

use Exception;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Repositories\Category\CategoryRepository;

class CategoryController extends Controller
{
    private $categoryRepo;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepo = $categoryRepository;
    }
    
    public function index()
    {
        try {
            $categories = $this->categoryRepo->getCategories();

            return response()->json([
                'data' => CategoryResource::collection($categories)
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    public function store(StoreCategoryRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['uuid'] = Str::uuid();

            DB::beginTransaction();
            $this->categoryRepo->createCategory($validated);
            DB::commit();

            return response()->json([
                'message' => 'Data kategori berhasil ditambahkan'
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
            $category = $this->categoryRepo->showCategory($id);

            return response()->json([
                'data' => new CategoryResource($category)
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, $id)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();
                $this->categoryRepo->updateCategory($id, $validated);
            DB::commit();

            return response()->json([
                'message' => 'Data kategori berhasil diperbarui'
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
                $this->categoryRepo->deleteCategory($id);
            DB::commit();

            return response()->json([
                'message' => 'Data kategori berhasil dihapus'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return errorResponse($e->getMessage());
        }
    }
}
