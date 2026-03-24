<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'user_id', 'custom_request_id', 'order_number', 'customer_name', 
        'customer_phone', 'customer_email', 'delivery_address', 'delivery_date', 
        'total_amount', 'status', 'payment_method', 'driver_name'
    ];

    protected $casts = [
        'total_amount' => 'float',
        'delivery_date' => 'datetime',
    ];

    public function items() { return $this->hasMany(OrderItem::class); }
}