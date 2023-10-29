<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['uuid', 'name'];

    public function stuffs()
    {
        return $this->hasMany(Stuff::class);
    }
}
