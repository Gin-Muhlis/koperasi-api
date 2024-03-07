<?php

namespace App\Repositories\ProfileApp;

use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\ProfileApp;

class ProfileAppRepositoryImplement extends Eloquent implements ProfileAppRepository{


    protected $model;

    public function __construct(ProfileApp $model)
    {
        $this->model = $model;
    }

    public function getProfile(){
        return $this->model->latest()->first();
    }

    public function updateProfile($id, $data){
        $this->update($id, $data);
    }
}
