<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileApp extends Model
{
    use HasFactory;

   protected $fillable = ['name', 'icon', 'address', 'phone_number', 'uuid', 'about'] ;
}
