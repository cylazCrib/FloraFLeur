<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
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

    public function shop()
    {
        // If user is a vendor, they own a shop
        if ($this->role === 'vendor') {
             return $this->hasOne(Shop::class, 'user_id');
        }
        // If user is staff, they belong to a shop
        return $this->belongsTo(Shop::class, 'shop_id');
    }
}