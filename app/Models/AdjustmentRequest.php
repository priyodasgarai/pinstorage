<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdjustmentRequest extends Model
{
    use HasFactory;
    protected $table = "adjustment_requests";
    protected $fillable = [
    	'user_id',
    	'order_id',
    	'comments',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];
}
