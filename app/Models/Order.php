<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];

    // An order belongs to a Shop
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    // An order belongs to a Customer (User)
    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // An order has many items
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function notifications()
{
    return $this->hasMany(OrderNotification::class);
}
}