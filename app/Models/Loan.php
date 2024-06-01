<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'amount',
        'date',
        'status',
        'deadline',
        'loan_duration',
        'loan_interest',
        'date_completion',
        'total_payment',
        'member_id',
        'sub_category_id',
        'user_id',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
        'deadline' => 'date',
        'date_completion' => 'date',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function installments()
    {
        return $this->hasMany(Installment::class);
    }
}
