<?php

namespace App\Repositories\PaymentDetermination;

use LaravelEasyRepository\Repository;

interface PaymentDeterminationRepository extends Repository{

    public function createPaymentDetermination($request);
    public function memberPayment($member_id, $month_year);
}
