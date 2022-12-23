<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $table = "carts";
    protected $fillable = [
        'user_id',
        'product_id',
        'status',
        'quantity',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'


    ];
}
