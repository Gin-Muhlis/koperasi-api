<?php

namespace App\Repositories\Product;

use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\Product;

class ProductRepositoryImplement extends Eloquent implements ProductRepository{

    
    protected $model;

    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    public function getProducts() {
        return $this->all();
    }

    public function createProduct($request) {
        return $this->create($request);
    }

    public function showProduct($id) {
        return $this->findOrFail($id);
    }

    public function updateProduct($id, $request) {
        return $this->update($id, $request);
    }

    public function deleteProduct($id) {
        return $this->delete($id);
    }
}
