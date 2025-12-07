<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
    'name',
    'email',
    'password',
    'role', 
    'shop_id',
];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
        // If user is owner (has one shop) OR staff (belongs to shop)
        // For simplicity in this setup, we treat 'shop_id' on users table as the link
        // But your Owner logic might use 'user_id' on shops table. 
        // Let's support both:
        
        if ($this->role === 'vendor') {
             return $this->hasOne(Shop::class, 'user_id');
        }
        
        return $this->belongsTo(Shop::class, 'shop_id');
    }
}
