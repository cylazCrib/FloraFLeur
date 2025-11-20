<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    use HasFactory;

    protected $guarded = []; // <--- THIS LINE IS CRITICAL

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}