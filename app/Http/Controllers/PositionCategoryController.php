<?php

namespace App\Http\Controllers;

require_once app_path() . '/Helpers/helpers.php';

use App\Models\PositionCategory;
use App\Http\Requests\StorePositionCategoryRequest;
use App\Http\Requests\UpdatePositionCategoryRequest;
use App\Http\Resources\PositionCategoryResource;
use App\Repositories\PositionCategory\PositionCategoryRepository;
use Exception;

class PositionCategoryController extends Controller
{
    private $positionCategoryRepo;

    public function __construct(PositionCategoryRepository $positionCategoryRepository)
    {
        $this->positionCategoryRepo = $positionCategoryRepository;
    }
    
    public function index()
    {
        try {
            $positionCategories = $this->positionCategoryRepo->getPositionCategories();

            return response()->json([
                'data' => PositionCategoryResource::collection($positionCategories)
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    public function store(StorePositionCategoryRequest $request)
    {
        try {
            $validated = $request->validated();

            $this->positionCategoryRepo->createPositionCategory($validated);

            return response()->json([
                'message' => 'Golongan anggota berhasil ditambahkan'
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    public function update(UpdatePositionCategoryRequest $request, PositionCategory $positionCategory)
    {
        try {

            $validated = $request->validated();

            $this->positionCategoryRepo->updatePositionCategory($positionCategory->id, $validated);

            return response()->json([
                'message' => 'Golongan anggota berhasil diperbarui'
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    public function destroy(PositionCategory $positionCategory)
    {
        try {
            $this->positionCategoryRepo->deletePositionCategory($positionCategory->id);

            return response()->json([
                'message' => 'Golongan anggota berhasil dihapus'
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }
}
