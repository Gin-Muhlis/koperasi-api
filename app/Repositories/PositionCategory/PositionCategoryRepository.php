<?php

namespace App\Repositories\PositionCategory;

use LaravelEasyRepository\Repository;

interface PositionCategoryRepository extends Repository
{
    public function getPositionCategories();
}
