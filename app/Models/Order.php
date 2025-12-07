<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'user_id',
        'order_number',
        'customer_name',
        'customer_phone',
        'customer_email',
        'delivery_address',
        'delivery_date',
        'total_amount',
        'status',
        'payment_method', // <--- THIS IS CRITICAL
        'driver_name'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}