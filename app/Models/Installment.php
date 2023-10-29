<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    use HasFactory;

    protected $fillable = ['uuid', 'code', 'amount', 'loan_id'];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
