<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shop extends Model
{
    use HasFactory;

    // Allow mass assignment
    protected $guarded = [];

    /**
     * Get the user (owner) of the shop.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the products for the shop.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the inventory items for the shop.
     */
    public function inventory(): HasMany
    {
        return $this->hasMany(InventoryItem::class);
    }

    /**
     * Get the orders for the shop.
     * (THIS IS THE MISSING PART)
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function staff()
{
    return $this->hasMany(Staff::class);
}
}