<?php

namespace App\Repositories\Category;

use LaravelEasyRepository\Repository;

interface CategoryRepository extends Repository
{

    public function getCategories();
    public function createCategory($request);
    public function showCategory($id);
    public function updateCategory($id, $request);
    public function deleteCategory($id);
}
