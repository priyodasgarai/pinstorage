<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomizeOrder extends Model
{
    use HasFactory;
    protected $table = "customize_orders";
    protected $fillable = [
    	'order_id',
    	'addon_id',
    	'addon_price',
    	'instruction',
    	'image',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];
     public function ProductDesignAddons()
    {
        return $this->hasOne(ProductDesignAddons::class,'id','addon_id');
    }
     public function addonsImages()
    {
        return $this->hasOne(ProductDesignAddonsImages::class,'id','addon_id');
    }
}
