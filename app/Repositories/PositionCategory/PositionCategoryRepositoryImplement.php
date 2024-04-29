<?php

namespace App\Repositories\PositionCategory;

use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\PositionCategory;

class PositionCategoryRepositoryImplement extends Eloquent implements PositionCategoryRepository
{


    protected $model;

    public function __construct(PositionCategory $model)
    {
        $this->model = $model;
    }
    public function getPositionCategories()
    {
        return $this->model->latest()->get();
    }

    public function createPositionCategory($data)
    {
        $this->create($data);
    }

    public function updatePositionCategory($id, $data)
    {
        $this->update($id, $data);
    }

    public function deletePositionCategory($id)
    {
        $this->delete($id);
    }

    public function getPositionCategoryById($id) {
        $this->model->find($id);
    }
}
