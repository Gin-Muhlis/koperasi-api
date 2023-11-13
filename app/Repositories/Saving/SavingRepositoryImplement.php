<?php

namespace App\Repositories\Saving;

use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\Saving;

class SavingRepositoryImplement extends Eloquent implements SavingRepository{

    /**
    * Model class to be used in this repository for the common methods inside Eloquent
    * Don't remove or change $this->model variable name
    * @property Model|mixed $model;
    */
    protected $model;

    public function __construct(Saving $model)
    {
        $this->model = $model;
    }

    public function makeSavingMembers($data)
    {
        return $this->create($data);
    }
}
