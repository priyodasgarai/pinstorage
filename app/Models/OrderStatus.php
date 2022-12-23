<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    use HasFactory;
    protected $table = "order_statuses";
    protected $fillable = [
    	'order_id',
        'order_status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];
    /*public function orderstatus()
    {
        return $this->belongsTo(Order::class,'order_id');
    }*/
}
