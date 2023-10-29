<?php

namespace App\Repositories\User;

use LaravelEasyRepository\Repository;

interface UserRepository extends Repository{

    public function createUser($request);
    public function updateUser($id, $request);
}
