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
        'phone_number',
        'gender',
        'identity_number',
        'religion',
        'image',
        'date_activation',
        'user_id',
    ];

    protected $searchableFields = ['*'];

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
