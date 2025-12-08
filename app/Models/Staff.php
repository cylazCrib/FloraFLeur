<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    // Explicitly define the table name if it's not the plural of the model
    protected $table = 'staff'; 

    protected $fillable = [
        'shop_id',
        'name',
        'email',
        'phone',
        'role',
        'status',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}