<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfileApp extends Model
{
    use HasFactory, SoftDeletes;

   protected $fillable = ['chairmans_name', 'secretary_name', 'treasurer_name', 'address','uuid'] ;
}
