<?php

namespace App\Repositories\SubCategory;

use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\SubCategory;

class SubCategoryRepositoryImplement extends Eloquent implements SubCategoryRepository
{


    protected $model;

    public function __construct(SubCategory $model)
    {
        $this->model = $model;
    }

    public function getSubCategories()
    {
        return $this->all();
    }

    public function createSubCategory($request)
    {
        return $this->create($request);
    }

    public function showSubCategory($id)
    {
        return $this->findOrFail($id);
    }

    public function updateSubCategory($id, $request)
    {
        return $this->update($id, $request);
    }

    public function deleteSubCategory($id)
    {
        return $this->delete($id);
    }

    public function getByCategory($category)
    {
        return $this->model::where('category_id', $category)->get();
    }
}
