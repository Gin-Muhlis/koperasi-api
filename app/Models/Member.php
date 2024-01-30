<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'email',
        'name',
        'address',
        'position',
        'phone_number',
        'gender',
        'identity_number',
        'religion',
        'image',
        'date_activation',
        'user_id',
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function savings()
    {
        return $this->hasMany(Saving::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function paymentDeterminations()
    {
        return $this->hasMany(PaymentDetermination::class);
    }
}
