<?php

namespace App\Repositories\Purchase;

use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\Purchase;

class PurchaseRepositoryImplement extends Eloquent implements PurchaseRepository{

    protected $model;

    public function __construct(Purchase $model)
    {
        $this->model = $model;
    }

    public function getPurchases() {
        return $this->all();
    }

    public function createPurchase($request) {
        return $this->create($request);
    }

    public function showPurchase($id) {
        return $this->findOrFail($id);
    }

    public function updatePurchase($id, $request) {
        return $this->update($id, $request);
    }

    public function deletePurchase($id) {
        return $this->delete($id);
    }
}
