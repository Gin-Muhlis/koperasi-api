<?php

namespace App\Repositories\PaymentDetermination;

use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\PaymentDetermination;

class PaymentDeterminationRepositoryImplement extends Eloquent implements PaymentDeterminationRepository{

    /**
    * Model class to be used in this repository for the common methods inside Eloquent
    * Don't remove or change $this->model variable name
    * @property Model|mixed $model;
    */
    protected $model;

    public function __construct(PaymentDetermination $model)
    {
        $this->model = $model;
    }

    public function createPaymentDetermination($data)
    {
        return $this->create($data);
    }

    public function memberPayment($member_id, $month_year)
    {
        return $this->model->where([
            ['member_id', $member_id],
            ['payment_month', $month_year]
        ])->first();
    }
}
