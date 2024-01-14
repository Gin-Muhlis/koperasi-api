<?php

namespace App\Repositories\User;

use LaravelEasyRepository\Repository;

interface UserRepository extends Repository{

    public function getUsers();
    public function createUser($request);
    public function showUser($id);
    public function updateUser($id, $request);
    public function deleteUser($id);
}
