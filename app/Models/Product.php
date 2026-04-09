<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute; // REQUIRED IMPORT

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

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'price' => 'float',
        'quantity' => 'integer',
    ];

    /**
     * This ensures the dynamic "image" URL is always included 
     * when the model is converted to JSON for Inertia.
     */
    protected $appends = ['image'];

    /**
     * Get the shop that owns the product.
     */
    public function shop() 
    { 
        return $this->belongsTo(Shop::class); 
    }

    /**
     * Interact with the product's image URL.
     */
     
    }
