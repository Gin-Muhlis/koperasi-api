<?php

namespace App\Repositories\PurchaseDetail;

use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\PurchaseDetail;

class PurchaseDetailRepositoryImplement extends Eloquent implements PurchaseDetailRepository{


    protected $model;

    public function __construct(PurchaseDetail $model)
    {
        $this->model = $model;
    }

    public function getPurchaseDetails() {
        return $this->all();
    }

    public function createPurchaseDetail($request) {
        return $this->create($request);
    }

    public function showPurchaseDetail($id) {
        return $this->findOrFail($id);
    }

    public function updatePurchaseDetail($id, $request) {
        return $this->update($id, $request);
    }

    public function deletePurchaseDetail($id) {
        return $this->delete($id);
    }
}
