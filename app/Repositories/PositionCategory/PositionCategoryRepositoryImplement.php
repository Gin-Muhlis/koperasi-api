<?php

namespace App\Repositories\PositionCategory;

use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\PositionCategory;

class PositionCategoryRepositoryImplement extends Eloquent implements PositionCategoryRepository{

    /**
    * Model class to be used in this repository for the common methods inside Eloquent
    * Don't remove or change $this->model variable name
    * @property Model|mixed $model;
    */
    protected $model;

    public function __construct(PositionCategory $model)
    {
        $this->model = $model;
    }
    public function getPositionCategories()
    {
        return $this->all();
    }
}
