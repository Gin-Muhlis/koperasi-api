<?php

namespace App\Repositories\PaymentDetermination;

use LaravelEasyRepository\Repository;

interface PaymentDeterminationRepository extends Repository{

    public function createPaymentDetermination($request);
}
