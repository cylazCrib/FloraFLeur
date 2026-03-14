<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomRequest extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id', 
        'shop_id', 
        'description', 
        'status', 
        'budget', 
        'contact_number',
        'occasion',
        'date_needed',
        'color_preference',
        'reference_image_url',
        'vendor_quote'
    ];

    protected $casts = [
        'date_needed' => 'datetime',
        'budget' => 'decimal:2',
        'vendor_quote' => 'decimal:2',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function shop() { return $this->belongsTo(Shop::class); }
}