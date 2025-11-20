<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $guarded = []; // Allow mass assignment

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}