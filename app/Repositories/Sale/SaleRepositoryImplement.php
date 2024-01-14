<?php

namespace App\Repositories\Sale;

use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\Sale;

class SaleRepositoryImplement extends Eloquent implements SaleRepository{

   
    protected $model;

    public function __construct(Sale $model)
    {
        $this->model = $model;
    }

    public function getSales() {
        return $this->all();
    }

    public function createSale($request) {
        return $this->create($request);
    }

    public function showSale($id) {
        return $this->findOrFail($id);
    }

    public function updateSale($id, $request) {
        return $this->update($id, $request);
    }

    public function deleteSale($id) {
        return $this->delete($id);
    }
}
