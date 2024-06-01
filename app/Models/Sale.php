<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'date',
        'total_payment',
        'sub_category_id',
        'user_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function salesDetails()
    {
        return $this->hasMany(SalesDetail::class);
    }
}
