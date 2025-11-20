<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Allow mass assignment for all fields
    protected $guarded = [];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}