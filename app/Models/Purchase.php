<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'date_purchase',
        'total',
        'sub_category_id',
        'user_id',
    ];

    protected $casts = [
        'date_purchase' => 'date',
    ];

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
