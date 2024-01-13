<?php

namespace App\Repositories\Stuff;

use LaravelEasyRepository\Repository;

interface StuffRepository extends Repository{

    public function getStuffs();
    public function createStuff($request);
    public function showStuff($id);
    public function updateStuff($id, $request);
    public function deleteStuff($id);
}
