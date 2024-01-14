<?php

namespace App\Repositories\SalesDetail;

use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\SalesDetail;

class SalesDetailRepositoryImplement extends Eloquent implements SalesDetailRepository{

   protected $model;

    public function __construct(SalesDetail $model)
    {
        $this->model = $model;
    }

    public function getSalesDetails() {
        return $this->all();
    }

    public function createSalesDetail($request) {
        return $this->create($request);
    }

    public function showSalesDetail($id) {
        return $this->findOrFail($id);
    }

    public function updateSalesDetail($id, $request) {
        return $this->update($id, $request);
    }

    public function deleteSalesDetail($id) {
        return $this->delete($id);
    }
}
