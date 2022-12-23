<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;
    protected $table = "deliveries";
    protected $fillable = [
        'delivery_title',
        'delivery_description',
        'day',
        'price',
        'status',
        'created_by',
        'updated_by'
    ];
}
