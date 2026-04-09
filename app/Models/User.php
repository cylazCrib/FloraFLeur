<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',      // <--- Required
        'shop_id',   // <--- CRITICAL: Allows linking to the shop
        'phone',     // <--- Required for staff details
        'status',    // <--- Required for Active/Suspended
        'address',   // <--- Required for delivery address
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // app/Models/User.php

    // 1. Define the "Vendor" relationship
    public function ownedShop()
    {
        return $this->hasOne(Shop::class, 'user_id');
    }

    // 2. Define the "Staff" relationship
    public function assignedShop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    // 3. Create the dynamic "shop" property
    public function getShopAttribute()
    {
        if (strtolower($this->role) === 'vendor') {
            return $this->ownedShop;
        }
        return $this->assignedShop;
    }
}