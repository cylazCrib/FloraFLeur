<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'name',
        'description',
        'price',
        'quantity',
        'image',
        'category',
        'occasion'
    ];

    // [FIX] Force price to be a number (float), not a string
    protected $casts = [
        'price' => 'float',
        'quantity' => 'integer',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}