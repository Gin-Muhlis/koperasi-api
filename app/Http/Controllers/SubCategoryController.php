<?php

namespace App\Http\Controllers;

require_once app_path() . '/Helpers/helpers.php';

use Exception;
use App\Models\SubCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\SubCategoryResource;
use App\Http\Requests\StoreSubCategoryRequest;
use App\Http\Requests\UpdateSubCategoryRequest;
use App\Repositories\Category\CategoryRepository;
use App\Repositories\SubCategory\SubCategoryRepository;

class SubCategoryController extends Controller
{
    private $subCategoryRepo;
    private $categoryRepo;

    public function __construct(SubCategoryRepository $subCategoryRepository, CategoryRepository $categoryRepository)
    {
        $this->subCategoryRepo = $subCategoryRepository;
        $this->categoryRepo = $categoryRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $sub_categories = $this->subCategoryRepo->getSubCategories();

            return response()->json([
                'data' => SubCategoryResource::collection($sub_categories)
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubCategoryRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['uuid'] = Str::uuid();
            $validated['code'] = generateCode();

            DB::beginTransaction();

            $this->subCategoryRepo->createSubCategory($validated);

            DB::commit();

            return response()->json([
                'message' => 'Data sub kategori berhasil ditambahkan'
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
            $category = $this->subCategoryRepo->showSubCategory($id);

            return response()->json([
                'data' => new SubCategoryResource($category)
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubCategoryRequest $request, $id)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $this->subCategoryRepo->updateSubCategory($id, $validated);

            DB::commit();

            return response()->json([
                'message' => 'Data sub kategori berhasil diperbarui'
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

            $this->subCategoryRepo->deleteSubcategory($id);

            DB::commit();

            return response()->json([
                'message' => 'Data sub kategori berhasil dihapus'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return errorResponse($e->getMessage());
        }
    }

    public function subCategorySaving()
    {
        try {
            $savingCategory = $this->categoryRepo->getByName('simpanan');
            $sub_categories = $this->subCategoryRepo->getByCategory($savingCategory->id);
            return response()->json([
                'data' => SubCategoryResource::collection($sub_categories)
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }
}
