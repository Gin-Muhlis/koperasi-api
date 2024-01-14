<?php

namespace App\Repositories\User;

use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\User;

class UserRepositoryImplement extends Eloquent implements UserRepository{

    
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function getUsers() {
        return $this->all();
    }

    public function createUser($request) {
        return $this->create($request);
    }

    public function showUser($id) {
        return $this->findOrFail($id);
    }

    public function updateUser($id, $request) {
        return $this->update($id, $request);
    }

    public function deleteUser($id) {
        return $this->delete($id);
    }
}
