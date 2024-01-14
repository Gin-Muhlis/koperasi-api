<?php

namespace App\Repositories\PurchaseDetail;

use LaravelEasyRepository\Repository;

interface PurchaseDetailRepository extends Repository{

    public function getPurchaseDetails();
    public function createPurchaseDetail($request);
    public function showPurchaseDetail($id);
    public function updatePurchaseDetail($id, $request);
    public function deletePurchaseDetail($id);
}
