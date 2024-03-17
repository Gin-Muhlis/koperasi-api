<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileApp extends Model
{
    use HasFactory;

   protected $fillable = ['chairmans_name', 'secretary_name', 'treasurer_name', 'address','uuid'] ;
}
