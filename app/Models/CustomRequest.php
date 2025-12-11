<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomRequest extends Model
{
    protected $fillable = [
        'user_id', 'shop_id', 'description', 'status', 'budget', 'contact_number'
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function shop() { return $this->belongsTo(Shop::class); }
}