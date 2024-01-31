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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PositionCategory $positionCategory)
    {
        //
    }
}
