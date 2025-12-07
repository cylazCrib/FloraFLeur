<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'name',
        'code',
        'type',    // <--- THIS IS CRITICAL for separating Flowers/Items
        'quantity'
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}