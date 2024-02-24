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
    /**
     * Display a listing of the resource.
     */
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
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

    /**
     * Display the specified resource.
     */
    public function show(PositionCategory $positionCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PositionCategory $positionCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
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

    /**
     * Remove the specified resource from storage.
     */
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
