<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDateHistory extends Model
{
    use HasFactory;
    protected $table = "order_date_histories";
    protected $fillable = [
    	'order_id',
    	'order_date',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];
}
