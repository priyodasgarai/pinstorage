<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCoupon extends Model
{
    use HasFactory;
    protected $table = "user_coupon";

    protected $fillable = [
        'coupon_id',
        'user_id',
        'address',
        'usage_limit_per_user',
        'expiry_date',       
        'created_at',
        'updated_at'
    ];
     public function users() {
        return $this->belongsTo(User::class,  'user_id');
    }
}
