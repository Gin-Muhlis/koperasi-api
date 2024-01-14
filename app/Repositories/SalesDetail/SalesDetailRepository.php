<?php

namespace App\Repositories\SalesDetail;

use LaravelEasyRepository\Repository;

interface SalesDetailRepository extends Repository{

    public function getSalesDetails();
    public function createSalesDetail($request);
    public function showSalesDetail($id);
    public function updateSalesDetail($id, $request);
    public function deleteSalesDetail($id);
}
