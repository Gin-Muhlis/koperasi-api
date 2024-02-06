<?php

namespace App\Repositories\SubCategory;

use LaravelEasyRepository\Repository;

interface SubCategoryRepository extends Repository {

	public function getSubCategories();
	public function createSubCategory($request);
	public function showSubCategory($id);
	public function updateSubCategory($id, $request);
	public function deleteSubCategory($id);
	public function getByCategory($category);
	public function getByName($name);
}
