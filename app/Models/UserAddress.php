<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;
    protected $table = "user_addresses";
    protected $fillable = [
         'user_id',
        'full_name',
        'phone',
        'address',
        'atra_street_sector_vilager',
        'landmark',
        'pincode',
        'city_id',
        'state_id',
        'country_id',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];
}
