<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'qty',
        'unit_price',
        'sub_total',
        'purchase_id',
        'stuff_id',
    ];

    protected $table = 'purchase_details';

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function stuff()
    {
        return $this->belongsTo(Stuff::class);
    }
}
