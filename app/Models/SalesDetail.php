<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'qty',
        'unit_price',
        'sub_total',
        'sale_id',
        'stuff_id',
    ];

    protected $table = 'sales_details';

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function stuff()
    {
        return $this->belongsTo(Stuff::class);
    }
}
