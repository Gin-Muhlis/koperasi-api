<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PositionCategory extends Model
{
    use HasFactory;

    protected $fillable = ['position', 'pokok', 'wajib', 'wajib_khusus'];
}
