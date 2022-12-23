<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;
    protected $table = 'coupons';
    protected $fillable = [
    	'coupon_type',
    	'coupon_code',
    	'coupon_discount',
    	'usage_limit_per_user',
    	'start_date',
    	'end_date',
    	'created_by',
        'updated_by',
    	'status'
    ];
}
