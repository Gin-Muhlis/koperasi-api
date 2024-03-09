<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileApp extends Model
{
    use HasFactory;

   protected $fillable = ['app_name', 'chairmans_name', 'treasurer_name', 'icon', 'address', 'phone_number', 'uuid', 'about'] ;
}
