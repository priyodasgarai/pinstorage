<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderAdress extends Model
{
    use HasFactory;
    protected $table = "order_adresses";
    protected $fillable = [
    	'order_id',
    	'full_name',
    	'phone',
    	'address',
    	'atra_street_sector_vilager',
    	'landmark',
    	'pincode',
    	'city_id',
    	'state_id',
    	'country_id',
        'address_type',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];
}
