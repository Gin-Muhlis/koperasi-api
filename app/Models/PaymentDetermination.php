<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentDetermination extends Model
{
    use HasFactory;

    protected $fillable = ['uuid', 'member_id', 'sub_category_id', 'amount', 'payment_month'];

    public function scopeWherePaymentMonth($query, $monthYear)
    {
        return $query->where('payment_month', $monthYear);
    }

    public function member()
    {
        return $this->belongsTo(member::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }
}
