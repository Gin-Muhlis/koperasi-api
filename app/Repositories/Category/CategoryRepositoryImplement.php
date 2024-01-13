<?php

namespace App\Repositories\Category;

use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\Category;

class CategoryRepositoryImplement extends Eloquent implements CategoryRepository{

    
    protected $model;

    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    public function getCategories() {
        return $this->all();
    }

    public function createCategory($request) {
        return $this->create($request);
    }

    public function showCategory($id) {
        return $this->findOrFail($id);
    }

    public function updateCategory($id, $request) {
        return $this->update($id, $request);
    }

    public function deleteCategory($id) {
        return $this->delete($id);
    }
}
