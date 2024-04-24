<?php

namespace App\Repositories\User;

use LaravelEasyRepository\Repository;

interface UserRepository extends Repository{

    public function getUsers();
    public function createUser($request);
    public function showUser($id);
    public function updateUser($id, $request);
    public function getUserByMemberId($member_id);
    public function changePassword($data, $user_id);
}
