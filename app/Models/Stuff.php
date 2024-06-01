<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stuff extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['uuid', 'name', 'price', 'image', 'product_id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function salesDetails()
    {
        return $this->hasMany(SalesDetail::class);
    }
}
