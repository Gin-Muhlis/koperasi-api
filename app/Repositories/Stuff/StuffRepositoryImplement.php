<?php

namespace App\Repositories\Stuff;

use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\Stuff;

class StuffRepositoryImplement extends Eloquent implements StuffRepository{

    protected $model;

    public function __construct(Stuff $model)
    {
        $this->model = $model;
    }

    public function getSTuffs() {
        return $this->all();
    }

    public function createStuff($request) {
        return $this->create($request);
    }

    public function showStuff($id) {
        return $this->findOrFail($id);
    }

    public function updateStuff($id, $request) {
        return $this->update($id, $request);
    }

    public function deleteStuff($id) {
        return $this->delete($id);
    }
}
