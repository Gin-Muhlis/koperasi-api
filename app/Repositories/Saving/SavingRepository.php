<?php

namespace App\Repositories\Saving;

use LaravelEasyRepository\Repository;

interface SavingRepository extends Repository{

    public function makeSavingMembers($data);
}
