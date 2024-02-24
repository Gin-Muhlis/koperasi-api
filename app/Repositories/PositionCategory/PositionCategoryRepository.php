<?php

namespace App\Repositories\PositionCategory;

use LaravelEasyRepository\Repository;

interface PositionCategoryRepository extends Repository
{
    public function getPositionCategories();
    public function createPositionCategory($data);
    public function updatePositionCategory($id, $data);
    public function deletePositionCategory($id);
}
