<?php

namespace App\Repositories\Category;

use App\Models\Category;
use LaravelEasyRepository\Implementations\Eloquent;

class CategoryRepositoryImplement extends Eloquent implements CategoryRepository {

	protected $model;

	public function __construct(Category $model) {
		$this->model = $model;
	}

	public function getCategories() {
		return $this->model->latest()->get();
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

	public function getByName($name) {
		return $this->model->where('name', $name)->first();
	}
}
