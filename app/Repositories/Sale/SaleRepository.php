<?php

namespace App\Repositories\Sale;

use LaravelEasyRepository\Repository;

interface SaleRepository extends Repository{

    public function getSales();
    public function createSale($request);
    public function showSale($id);
    public function updateSale($id, $request);
    public function deleteSale($id);
}
