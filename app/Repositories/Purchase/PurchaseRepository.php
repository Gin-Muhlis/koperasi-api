<?php

namespace App\Repositories\Purchase;

use LaravelEasyRepository\Repository;

interface PurchaseRepository extends Repository{

    public function getPurchases();
    public function createPurchase($request);
    public function showPurchase($id);
    public function updatePurchase($id, $request);
    public function deletePurchase($id);
}
