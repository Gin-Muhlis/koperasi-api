<?php

namespace App\Repositories\ProfileApp;

use LaravelEasyRepository\Repository;

interface ProfileAppRepository extends Repository{

    public function getProfile();
    public function updateProfile($id, $data);
}
