<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'product_id', 'product_name', 'quantity', 'price', 'image'];

    // FIX: Ensures numbers are clean and math works in Vue
    protected $casts = [
        'price' => 'float',
        'quantity' => 'integer',
    ];

    public function order() { return $this->belongsTo(Order::class); }
}