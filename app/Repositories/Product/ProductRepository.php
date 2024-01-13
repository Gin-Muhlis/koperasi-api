<?php

namespace App\Repositories\Product;

use LaravelEasyRepository\Repository;

interface ProductRepository extends Repository{

    public function getProducts();
    public function createProduct($request);
    public function showProduct($id);
    public function updateProduct($id, $request);
    public function deleteProduct($id);
}
