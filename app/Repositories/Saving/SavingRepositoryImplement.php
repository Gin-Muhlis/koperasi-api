<?php

namespace App\Repositories\Saving;

use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\Saving;

class SavingRepositoryImplement extends Eloquent implements SavingRepository{

   
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
