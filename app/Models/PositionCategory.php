<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PositionCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['position', 'pokok', 'wajib', 'wajib_khusus'];
}
